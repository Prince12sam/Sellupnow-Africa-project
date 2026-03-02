<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Modules\Wallet\app\Models\Wallet;
use Modules\Membership\app\Http\Services\MembershipService;

class SocialLoginController extends Controller
{
    protected $membershipService;

    public function __construct()
    {
        if (moduleExists("Membership")) {
            if (membershipModuleExistsAndEnable('Membership')) {
                $this->membershipService = app()->make(MembershipService::class);
            }
        }
    }

    public function facebook_redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebook_callback()
    {
        try {
            $user_fb_details = Socialite::driver('facebook')->user();
            $user_details = User::where('email', $user_fb_details->getEmail())->first();

            if ($user_details) {
                Auth::login($user_details);
                return redirect()->route('user.dashboard');
            } else {
                $new_user = User::create([
                    'username' => 'fb_' . explode('@', $user_fb_details->getEmail())[0],
                    'first_name' => $user_fb_details->getName(),
                    'last_name' => $user_fb_details->getName(),
                    'email' => $user_fb_details->getEmail(),
                    'email_verified' => 1,
                    'facebook_id' => $user_fb_details->getId(),
                    'password' => Hash::make(\Illuminate\Support\Str::random(8))
                ]);

                Auth::login($new_user);
                return redirect()->route('user.dashboard');
            }
        } catch (\Exception $e) {
            return redirect()->intended('login')->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
    }

    public function google_redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function google_callback()
    {
        try {
            $user_go_details = Socialite::driver('google')->user();
            $user_details = User::where('email', $user_go_details->getEmail())->first();

            if ($user_details) {
                Auth::login($user_details);
                return redirect()->route('user.dashboard');
            } else {
                $new_user = User::create([
                    'username' => 'go_' . explode('@', $user_go_details->getEmail())[0],
                    'first_name' => $user_go_details->getName(),
                    'last_name' => $user_go_details->getName(),
                    'email' => $user_go_details->getEmail(),
                    'email_verified' => 1,
                    'google_id' => $user_go_details->getId(),
                    'password' => Hash::make(\Illuminate\Support\Str::random(8))
                ]);

                // if exists wallet module
                if(moduleExists("Wallet")){
                    Wallet::create([
                        'user_id' => $new_user->id,
                        'balance' => 0,
                        'remaining_balance' => 0,
                        'withdraw_amount' => 0,
                        'status' => 1
                    ]);
                }

                // Create membership
                if ($new_user) {
                    if (moduleExists("Membership")) {
                        if (membershipModuleExistsAndEnable('Membership')) {
                            $this->membershipService->createFreeMembership($new_user);
                        }
                    }
                }

                Auth::login($new_user);
                return redirect()->route('user.dashboard');
            }
        } catch (\Exception $e) {
            return redirect()->intended('login')->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
    }
}
