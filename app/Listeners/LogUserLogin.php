<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request; // Import Request

class LogUserLogin
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
    public function handle(Login $event): void
    {
        if ($event->user) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'activity_type' => 'logged_in',
                'description' => 'Pengguna telah login.',
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
            ]);
        }
    }
}
