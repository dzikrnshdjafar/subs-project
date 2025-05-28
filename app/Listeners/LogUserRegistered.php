<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request; // Import Request

class LogUserRegistered
{
    protected Request $request;

    /**
     * Create the event listener.
     */
    public function __construct(Request $request) // Inject Request
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if ($event->user) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'activity_type' => 'registered',
                'description' => 'Pengguna telah mendaftar.',
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
            ]);
        }
    }
}
