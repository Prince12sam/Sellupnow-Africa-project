<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return Route::has('user.login') ? route('user.login') : '/login';
            }
            if ($request->is('user-home') || $request->is('user-home/*')) {
                return Route::has('user.login') ? route('user.login') : '/login';
            }
            return Route::has('user.login') ? route('user.login') : '/login';
        }
    }

}

