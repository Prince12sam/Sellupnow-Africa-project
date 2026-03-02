<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard == 'admin' && Auth::guard($guard)->check()){
            if (!Route::has('admin.dashboard')) {
                Auth::guard($guard)->logout();
                return $next($request);
            }
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }

}
