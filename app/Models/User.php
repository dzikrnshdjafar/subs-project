<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Subscription;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Helper untuk mendapatkan langganan aktif (bisa disesuaikan)
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest('ends_at');
    }

    // Helper untuk memeriksa apakah pengguna memiliki langganan aktif untuk plan tertentu
    public function hasActiveSubscriptionTo(string $planSlug): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->whereHas('plan', function ($query) use ($planSlug) {
                $query->where('slug', $planSlug);
            })
            ->where(function ($query) {
                $query->whereNull('ends_at') // Untuk paket yang tidak kedaluwarsa
                    ->orWhere('ends_at', '>', now()); // Atau yang belum kedaluwarsa
            })
            ->exists();
    }

    // Helper untuk mendapatkan plan saat ini
    public function getCurrentPlan(): ?Plan
    {
        $activeSub = $this->activeSubscription()->first();
        return $activeSub ? $activeSub->plan : null;
    }
}
