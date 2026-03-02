<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class guestMediaUploadCheck
{

    public function handle(Request $request, Closure $next)
    {
        // Allow guest access for uploading images
        if (url()->previous() == url('/listing/guest/add-listing') && $request->isMethod('post')){
            return $next($request);
        }else{
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    }
}

