<?php
// app/Listeners/LogOutOfOtherDevices.php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Untuk debugging
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Untuk mendapatkan ID sesi saat ini

class LogOutOfOtherDevices
{
    protected $request;

    /**
     * Create the event listener.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event): void
    {
        // $event->user adalah pengguna yang baru saja login.
        // $event->guard adalah guard yang digunakan (misalnya 'web').
        // $event->remember adalah boolean apakah 'remember me' digunakan.

        $user = $event->user;
        $currentSessionId = Session::getId(); // Dapatkan ID sesi yang BARU dibuat untuk login ini

        // Pastikan kita menggunakan session driver database
        if (config('session.driver') !== 'database') {
            Log::warning("SESSION_DRIVER is not 'database'. Cannot log out other devices for user ID: {$user->id}.");
            return;
        }

        Log::info("User ID {$user->id} logged in with new session ID: {$currentSessionId}. Attempting to clear old sessions.");

        try {
            // Hapus semua sesi lain untuk user_id ini, KECUALI sesi yang baru saja dibuat
            $deletedSessionsCount = DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId) // Jangan hapus sesi saat ini
                ->delete();

            if ($deletedSessionsCount > 0) {
                Log::info("Successfully deleted {$deletedSessionsCount} old session(s) for user ID: {$user->id}.");
            } else {
                Log::info("No old sessions found to delete for user ID: {$user->id}, or they were already cleared.");
            }
        } catch (\Exception $e) {
            Log::error("Error deleting old sessions for user ID: {$user->id}. Error: " . $e->getMessage());
        }
    }
}
