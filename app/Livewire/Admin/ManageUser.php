<?php

namespace App\Livewire\Admin;

use App\Jobs\SendEmailVerification;
use App\Models\Plan;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Carbon;

class ManageUser extends Component
{
    public User $user;
    public $plans, $selectedPlan;

    public function mount(User $user)
    {
        $this->user = $user->load(['purchases.plan', 'activePlan.plan']);
        $this->plans = Plan::all();
    }

    public function addPlan()
    {
        $this->validate([
            'selectedPlan' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($this->selectedPlan);
        $activePurchase = $this->user->activePlan;

        if ($activePurchase) {
            $newExpiresAt = $this->calculateExpiry($activePurchase->end_date, $plan);

            $activePurchase->update([
                'plan_id' => $plan->id,
                'amount_paid' => $activePurchase->amount_paid + $plan->price,
                'end_date' => $newExpiresAt
            ]);

            $message = 'Plan extended successfully!';
        } else {
            $expiresAt = $this->calculateExpiry(now(), $plan);

            $this->user->purchases()->create([
                'plan_id' => $plan->id,
                'amount_paid' => $plan->price,
                'start_date' => now(),
                'end_date' => $expiresAt,
                'status' => 'active',
            ]);

            $message = 'Plan added successfully!';
        }

        $this->selectedPlan = null;

        $this->user->refresh();

        $this->dispatch('sweetAlert', title: 'Success', message: $message, type: 'success');
    }

    public function cancelPurchase()
    {
        if ($this->user->activePlan) {
            $this->user->activePlan->update(['status' => 'cancelled']);
            $message = 'Purchase cancelled successfully!';
        } else {
            $message = 'No active purchase found!';
        }
        $this->dispatch('sweetAlert', title: 'Success', message: $message, type: 'success');

        $this->user->refresh();
    }

    public function verifyEmailManually()
    {
        $this->user->update(['email_verified_at' => now()]);
        $this->dispatch('sweetAlert', title: 'Success', message: 'Email verified manually.', type: 'success');
        $this->user->refresh();
    }

    public function resendVerificationEmail()
    {
        if (!$this->user->hasVerifiedEmail()) {
            SendEmailVerification::dispatch($this->user)->delay(now()->addSeconds(5));
            $this->dispatch('sweetAlert', title: 'Success', message: 'Verification email resent.', type: 'success');
        } else {
            $this->dispatch('sweetAlert', title: 'Info', message: 'Email is already verified.', type: 'info');
        }
    }

    public function banUser($reason = null)
    {
        if ($this->user->isBanned()) {
            $this->dispatch('sweetAlert', title: 'Info', message: 'User is already banned.', type: 'info');
            return;
        }

        if (! $this->user->isBanned()) {
            $this->user->update(['banned_at' => now(), 'ban_reason' => $reason]);
            $this->user->tokens()->delete();
            $this->dispatch('sweetAlert', title: 'Success', message: 'User banned successfully.', type: 'success');
        }
        $this->user->refresh();
    }

    public function unbanUser()
    {
        if ($this->user->isBanned()) {
            $this->user->update(['banned_at' => null, 'ban_reason' => null]);
            $this->dispatch('sweetAlert', title: 'Success', message: 'User unbanned successfully.', type: 'success');
        }
        $this->user->refresh();
    }

    public function deleteUser()
    {
        $this->user->delete();
        $this->dispatch('sweetAlert', title: 'Success', message: 'User deleted successfully.', type: 'success');
        return redirect()->route('users.all');
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.manage-user')
            ->extends('layouts.app')
            ->section('content');
    }

    private function calculateExpiry(Carbon $start, Plan $plan): Carbon
    {
        $maxDate = Carbon::create(2038, 1, 19, 3, 14, 7);
        $expiresAt = match ($plan->duration_unit) {
            'day' => $start->addDays($plan->duration),
            'week' => $start->addWeeks($plan->duration),
            'month' => $start->addMonths($plan->duration),
            'year' => $start->addYears($plan->duration),
            default => $start->addDays(7),
        };
        return $expiresAt->greaterThan($maxDate) ? $maxDate : $expiresAt;
    }
}
