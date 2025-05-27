<?php

namespace App\Http\Middleware;

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
     */
    public function handle(Request $request, Closure $next, ...$plans): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('login');
        }

        // Cek apakah langganan aktif dan belum kedaluwarsa
        $activeSubscription = $user->activeSubscription()->first();

        if (!$activeSubscription || !$activeSubscription->isActive()) {
            // Jika tidak punya langganan aktif ATAU sudah kedaluwarsa
            // Anda bisa redirect ke halaman billing atau tampilkan pesan
            return redirect()->route('subscription.plans')->with('error', 'Langganan Anda tidak aktif atau telah kedaluwarsa. Silakan perbarui langganan Anda.');
        }

        // Jika middleware dipanggil dengan parameter plan (slug)
        if (!empty($plans)) {
            $currentPlanSlug = $activeSubscription->plan->slug;
            $allowed = false;
            foreach ($plans as $planSlug) {
                if ($currentPlanSlug === $planSlug) {
                    $allowed = true;
                    break;
                }
                // Logika jika premium bisa akses basic, dst.
                if ($currentPlanSlug === 'premium' && ($planSlug === 'basic' || $planSlug === 'free')) {
                    $allowed = true;
                    break;
                }
                if ($currentPlanSlug === 'basic' && $planSlug === 'free') {
                    $allowed = true;
                    break;
                }
            }

            if (!$allowed) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini dengan paket saat ini. Silakan upgrade paket Anda.');
            }
        }

        return $next($request);
    }
}
