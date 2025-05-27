<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check and update status of expired subscriptions';

    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        $expiredSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', Carbon::now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return;
        }

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->status = 'expired';
            $subscription->save();
            $this->info("Subscription ID {$subscription->id} for User ID {$subscription->user_id} (Plan: {$subscription->plan->name}) has been marked as expired.");

            // Opsional: Berikan pengguna paket 'free' secara otomatis setelah paket berbayar berakhir
            // $freePlan = \App\Models\Plan::where('slug', 'free')->first();
            // if ($freePlan) {
            //     // Pastikan tidak ada langganan free yang aktif dulu
            //     $hasActiveFree = Subscription::where('user_id', $subscription->user_id)
            //                                 ->where('plan_id', $freePlan->id)
            //                                 ->where('status', 'active')
            //                                 ->exists();
            //     if (!$hasActiveFree) {
            //         Subscription::create([
            //             'user_id' => $subscription->user_id,
            //             'plan_id' => $freePlan->id,
            //             'starts_at' => Carbon::now(),
            //             'ends_at' => null,
            //             'status' => 'active',
            //         ]);
            //         $this->info("User ID {$subscription->user_id} has been downgraded to Free plan.");
            //     }
            // }
        }

        $this->info('Finished checking expired subscriptions.');
        return Command::SUCCESS;
    }
}
