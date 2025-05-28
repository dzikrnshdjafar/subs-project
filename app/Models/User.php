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

    public function activeSubscriptions(): HasMany // Mengembalikan banyak langganan aktif
    {
        return $this->hasMany(Subscription::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->with('plan'); // Eager load plan
    }

    // Method ini mungkin perlu diganti atau disesuaikan dengan kebutuhan Anda
    // karena pengguna bisa punya >1 plan aktif.
    // Ini contoh untuk mendapatkan plan dengan rank tertinggi yang aktif.
    public function getHighestActivePlan(): ?Plan
    {
        $activeSubs = $this->activeSubscriptions; // Menggunakan relasi yang sudah di-eager load jika memungkinkan
        if ($activeSubs->isEmpty()) {
            return null;
        }
        return $activeSubs->sortByDesc(function ($subscription) {
            return $subscription->plan ? $subscription->plan->rank : 0;
        })->first()->plan;
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'desc');
    }
}
