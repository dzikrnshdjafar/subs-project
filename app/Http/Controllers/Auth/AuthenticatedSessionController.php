<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $unusualActivityMessage = null;
        if (Auth::check()) { // Jika pengguna sudah login dan kembali ke halaman login (jarang terjadi)
            // Atau jika kita ingin mengambilnya untuk pengguna yang BELUM login tapi mungkin ada cache dari upaya sebelumnya
            // Ini lebih rumit karena kita tidak tahu ID pengguna sebelum login.
            // Jadi, lebih baik menampilkan ini SETELAH login berhasil.
        }
        // Untuk menampilkan sebelum login, kita perlu cara lain untuk mengidentifikasi pengguna,
        // atau menampilkan pesan umum jika ada flag global.
        // Untuk sekarang, kita akan fokus menampilkan setelah login.

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // Memicu event Login dan listener LogUserLogin

        $user = Auth::user(); // Dapatkan pengguna yang baru saja login
        $request->session()->regenerate(); // Regenerate sesi

        // Cek cache untuk peringatan aktivitas tidak biasa
        $cacheKey = 'unusual_activity_warning_' . $user->id;
        $activityData = Cache::pull($cacheKey); // Ambil dan hapus dari cache

        $redirectResponse = redirect()->intended(route('dashboard', absolute: false));

        if ($activityData) {
            $loginCount = $activityData['login_count'];
            $ipCount = $activityData['ip_count'];
            $ipAddressText = $ipCount . " different IP address" . ($ipCount > 1 ? "es" : "");

            $fullWarningMessage = "We’ve noticed unusual activity on your account.\n\n" .
                "You logged in {$loginCount} times today from {$ipAddressText}.\n\n" .
                "If you’re sharing your Groupy account, please stop now to avoid permanent suspension.";

            // Menggunakan session flash untuk pesan yang akan ditampilkan
            // (biasanya di dashboard atau layout utama setelah redirect)
            $redirectResponse->with('unusual_activity_alert', $fullWarningMessage);
        }

        return $redirectResponse;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
