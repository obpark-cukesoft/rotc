<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuthenticateWithLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /*public function handle($request, Closure $next)
    {
        return $next($request);
    }*/


    public function handle($request, Closure $next, $level, $api = false)
    {
        //dd($level, $api);
        if ($api) {
            $user = Auth::guard('api')->user(['api_token'=>$request->api_token]);
            if ($level <= $user->level ) {
                auth()->login($user);
                return $next($request);
            }
        }

        if ( Auth::check() ) {
            if ($level == User::LEVEL_ADMIN && Auth::user()->isAdmin()) {
                return $next($request);
            }

            if ($level == User::LEVEL_MEMBER && Auth::user()->isMember()) {
                return $next($request);
            }
        }

        Auth::logout();
        return redirect('login');
    }
}
