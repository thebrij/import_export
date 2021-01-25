<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (Auth::guest()) {
            //abort(401, 'This action is unauthorized.');
            return new Response(view('unauthorized')->with('role', 'ADMIN'));
        }

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        if (! Auth::user()->hasAnyRole($roles)) {
            //abort(401, 'This action is unauthorized.');
            return new Response(view('unauthorized')->with('role', 'ADMIN'));
        }

        return $next($request);
    }
}
