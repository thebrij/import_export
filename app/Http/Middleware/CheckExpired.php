<?php

namespace App\Http\Middleware;

use Closure;

class CheckExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check())
        {
            if(auth()->user()->hasRole('Administrator') or auth()->user()->hasRole('Manager') or auth()->user()->hasRole('Supervisor')) {

            }
            else if(auth()->user()->hasRole('User')) {
                if (date('Y-m-d H:i:s') > auth()->user()->expiry_date) {
                    $message = 'Your account has been Expired. Contact admin to get more credit';
                    auth()->logout();
                    return redirect()->route('login')->withMessage($message);
                }
            }
            else {
                $message = 'You are not Allowed to Login. Contact Admin to Get Your Login Activated';
                auth()->logout();
                return redirect()->route('login')->withMessage($message);
            }
        }
        return $next($request);
    }
}
