<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //check if authenticate && second is condition when we need to redirect i.e,
        if(Auth::guard($guard)->check() && $request->route()->named('login') ) {
            return redirect()->route('home');
        }

            return $next($request);
    }
}
