<?php

namespace App\Http\Middleware;

use App\Models\Plan; // Pastikan ini di-import
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$requiredPlanSlugs Slug dari plan yang dibutuhkan
     */
    public function handle(Request $request, Closure $next, ...$requiredPlanSlugs): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('login');
        }

        $activeUserSubscriptions = $user->activeSubscriptions()->get(); // Ambil semua langganan aktif

        if ($activeUserSubscriptions->isEmpty()) {
            // Jika tidak ada langganan aktif sama sekali
            return redirect()->route('subscription.plans')->with('error', 'Langganan Anda tidak aktif. Silakan perbarui langganan Anda.');
        }

        // Jika middleware dipanggil dengan parameter slug plan yang dibutuhkan
        if (!empty($requiredPlanSlugs)) {
            $isAllowed = false;
            foreach ($activeUserSubscriptions as $activeSub) {
                $currentUserPlan = $activeSub->plan; // Ambil model Plan dari langganan aktif

                if (!$currentUserPlan) continue; // Lewati jika relasi plan tidak ada

                // Cek apakah rank plan saat ini memenuhi syarat untuk salah satu slug yang dibutuhkan
                foreach ($requiredPlanSlugs as $requiredPlanSlug) {
                    $requiredPlanFromDb = Plan::where('slug', $requiredPlanSlug)->first();
                    if (!$requiredPlanFromDb) continue; // Lewati jika slug yang diminta tidak ada

                    // Akses diizinkan jika slug plan sama, atau rank plan saat ini >= rank plan yang dibutuhkan
                    if ($currentUserPlan->slug === $requiredPlanSlug || (isset($currentUserPlan->rank) && $currentUserPlan->rank >= $requiredPlanFromDb->rank)) {
                        $isAllowed = true;
                        break; // Keluar dari loop slug yang dibutuhkan
                    }
                }
                if ($isAllowed) {
                    break; // Keluar dari loop langganan aktif
                }
            }

            if (!$isAllowed) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini dengan paket aktif Anda saat ini.');
            }
        }
        // Jika tidak ada $requiredPlanSlugs yang diberikan, berarti hanya butuh langganan aktif apapun (sudah dicek di awal)

        return $next($request);
    }
}
