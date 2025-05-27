<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignFreePlanToNewUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // $freePlan = Plan::where('slug', 'free')->first();

        // if ($freePlan && $event->user) {
        //     Subscription::create([
        //         'user_id' => $event->user->id,
        //         'plan_id' => $freePlan->id,
        //         'starts_at' => Carbon::now(),
        //         'ends_at' => null, // Paket free tidak ada kedaluwarsa
        //         'status' => 'active',
        //     ]);
        // }
    }
}
