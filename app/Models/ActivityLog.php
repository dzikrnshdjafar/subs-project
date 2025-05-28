<?php

namespace App\Models;

use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Definisikan accessor untuk mendapatkan informasi browser yang lebih ringkas
    public function getDeviceInfoAttribute(): string
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        $agent = new Agent();
        $agent->setUserAgent($this->user_agent);

        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $platform = $agent->platform();
        // $platformVersion = $agent->version($platform); // Versi platform kadang tidak selalu relevan atau ada

        if ($browser && $browserVersion) {
            return $browser . '/' . $browserVersion . ' on ' . $platform;
        } elseif ($browser) {
            return $browser . ' on ' . $platform;
        } elseif ($platform) {
            return 'Unknown Browser on ' . $platform;
        }

        // Fallback jika parsing tidak menghasilkan apa-apa yang spesifik
        // Anda bisa memotong string user_agent jika terlalu panjang
        return Str::limit($this->user_agent, 50); // Menggunakan Str::limit untuk membatasi panjang
    }
}
