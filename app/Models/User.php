<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, Notifiable, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'role',
        'email_verified_at',
        'last_login',
        'banned_at',
        'ban_reason',
        'remember_token',
        'google_id',
        'apple_id',
        'is_premium',
        'subscription_status',
        'subscribed_product',
        'subscription_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'last_login' => 'datetime',
            'is_premium' => 'boolean',
            'subscription_expires_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    public function billingAddress()
    {
        return $this->hasOne(BillingAddress::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function activePlan()
    {
        return $this->hasOne(Purchase::class)->where('status', 'active')->where('end_date', '>', now())->latest();
    }

    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasAnyRole(string ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->active();
    }

    // Helper methods
    public function hasPremiumAccess(): bool
    {
        return $this->is_premium &&
            $this->activeSubscription()->exists();
    }

    public function getSubscriptionDaysRemaining(): int
    {
        $activeSubscription = $this->activeSubscription;
        return $activeSubscription ? $activeSubscription->getRemainingDays() : 0;
    }
}
