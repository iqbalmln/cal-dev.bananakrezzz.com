<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SessionTimeout
{
    public function handle($request, Closure $next)
    {
        $timeout = config('session.lifetime') ; // Timeout dalam detik

        // Jika pengguna login dan sesi memiliki waktu terakhir aktivitas
        if (Auth::check()) {
            $lastActivity = Session::get('lastActivityTime');
            $currentTime = Carbon::now()->timestamp;

            if ($lastActivity && ($currentTime - $lastActivity) > $timeout) {
                // Logout otomatis
                Auth::logout();
                Session::forget('lastActivityTime');
                return redirect('/login')->with('status', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // Perbarui waktu aktivitas terakhir
            Session::put('lastActivityTime', $currentTime);
        }

        return $next($request);
    }
}
