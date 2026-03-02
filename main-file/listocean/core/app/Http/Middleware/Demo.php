<?php

namespace App\Http\Middleware;

use Brian2694\Toastr\Facades\Toastr;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Demo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $not_allow_path = [
            'admin',
            'user',
            'membership/buy',
            'membership/renew',
            'home/advertisement/impression/store',
        ];
        $allow_path = [
            'user/logout',
            'membership/user/login',
            'admin',
            'user',
            'user-register',
            'user/live/fetch-chat-member-record',
            'user/member/live/fetch-chat-user-record',
            'user/live/message-send',
            'user/member/live/message-send',
            'broadcasting/auth',
        ];
        $contains = Str::contains($request->path(), $not_allow_path);
        if($request->isMethod('POST') || $request->isMethod('PUT')) {
            if($contains && !in_array($request->path(),$allow_path)){
                if ($request->ajax()){
                    // if user profile update
                    if ($request->is('user/profile/edit-profile')){
                        return response()->json([
                            'status'=>'demo_route_on',
                        ]);
                    }
                    return response()->json(['type' => 'warning' , 'msg' => 'This is demonstration purpose only, you may not able to change few settings, once your purchase this script you will get access to all settings.']);
                }
                toastr_warning('This is demonstration purpose only, you may not able to change few settings, once your purchase this script you will get access to all settings.');
                return redirect()->back()->with(['type' => 'warning' , 'msg' => 'This is demonstration purpose only, you may not able to change few settings, once your purchase this script you will get access to all settings.']);
            }

        }

        return $next($request);
    }
}
