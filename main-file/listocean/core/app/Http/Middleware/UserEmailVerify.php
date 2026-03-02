<?php

namespace App\Http\Middleware;

use Closure;

class UserEmailVerify
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
        if(!empty(get_static_option('user_email_verify_enable_disable'))){
            if (auth('web')->check() && auth('web')->user()->email_verified == 0 && !empty(get_static_option('user_email_verify_enable_disable')) && request()->path() !== 'user/logout'){
                return redirect()->route('email.verify');
            }
        }elseif((moduleExists('SMSGateway') && isPluginActive('SMSGateway')) && get_static_option('otp_login_status')){
            if(!empty(get_static_option('otp_login_status'))){
                if (auth('web')->check() && auth('web')->user()->otp_verified == 0 && !empty(get_static_option('otp_login_status')) && request()->path() !== 'user/logout'){
                    session()->put('auth_user_id', auth('web')->user()->id);
                    return redirect()->route('user.login.otp.verification');
                }
            }
        }

        return $next($request);
    }
}
