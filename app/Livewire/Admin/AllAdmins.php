<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AllAdmins extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 5;

    #[Url]
    public ?string $lastLoginStart = null;
    #[Url]
    public ?string $lastLoginEnd = null;

    public $userId;
    public $name;
    public $email;
    public $role;
    public $password;
    public $password_confirmation;
    public $isEdit = false;

    public function resetFilters()
    {
        $this->reset([
            'lastLoginStart',
            'lastLoginEnd',
        ]);
    }

    protected function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $this->isEdit
                ? 'unique:users,email,' . $this->userId
                : 'unique:users,email'],
            'role' => $this->isEdit ? ['required', 'in:admin,user'] : ['nullable', 'in:admin,user'], // Role is required only when editing
        ];

        // Only require password when creating user
        if (!$this->isEdit) {
            $rules['password'] = [
                'required',
                'confirmed',
                Password::min(6)->mixedCase()->numbers()->symbols()
            ];
        } elseif ($this->password) {
            // Optional password change on edit
            $rules['password'] = ['nullable', 'confirmed', Password::min(6)->mixedCase()->numbers()->symbols()];
        }

        return $rules;
    }

    public function resetForm()
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'role',
            'password',
            'password_confirmation',
        ]);
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function editUser($userId)
    {
        $this->resetForm();
        $this->isEdit = true;

        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
    }
    public function saveUser()
    {
        $this->validate();

        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ]);
            $message = 'User updated successfully.';
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'role' => 'admin',
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
            ]);
            $message = 'User created successfully.';
        }

        $this->dispatch('closeModel');
        $this->dispatch('sweetAlert', title: 'Success!', message: $message, type: 'success');
        $this->resetPage();
        $this->resetForm();
    }

    public function deleteUser($userid)
    {
        $user = User::findOrFail($userid);
        $user->delete();

        $this->dispatch('sweetAlert', title: 'Success!', message: 'User deleted successfully.', type: 'success');
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhereDate('created_at', $this->search);
                });
            })
            ->when($this->lastLoginStart && $this->lastLoginEnd, function ($query) {
                $query->whereBetween('last_login', [$this->lastLoginStart, $this->lastLoginEnd]);
            })
            ->where('role', '=', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-admins', compact('users'))
            ->extends('layouts.app')
            ->section('content');
    }
}
