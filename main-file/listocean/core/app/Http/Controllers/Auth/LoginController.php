<?php

namespace App\Http\Controllers\Auth;

use App\Models\Backend\Admin;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Frontend\AccountDeactivate;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function redirectTo()
    {
        return route('homepage');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        return view('auth.admin.login');
    }

    public function adminLogin(Request $request)
    {
        $email_or_username = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|min:6'
        ], [
            'username.required' => __($email_or_username.' required'),
            'password.required' => __('password required')
        ]);

        if (Auth::guard('admin')->attempt([$email_or_username => $request->username, 'password' => $request->password], $request->get('remember'))) {
            return response()->json([
                'msg' => __('Login Success Redirecting'),
                'type' => 'success',
                'status' => 'ok'
            ]);

        }
        return response()->json([
            'msg' => __('Your '.$email_or_username.' or password is wrong !!'),
            'type' => 'danger',
            'status' => 'not_ok',
        ]);
    }

    public function showAdminForgetPasswordForm()
    {
        return view('auth.admin.forget-password');
    }

    public function showAdminResetPasswordForm($username, $token)
    {
        return view('auth.admin.reset-password')->with([
            'username' => $username,
            'token' => $token
        ]);
    }

    public function sendAdminForgetPasswordMail(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string:max:191'
        ]);
        $user_info = Admin::where('username', $request->username)->orWhere('email', $request->username)->first();

        if(is_null($user_info)){
            return redirect()->back()->with([
                'msg' => __('your username or email does not found in our server'),
                'type' => 'danger'
            ]);
        }

        $token_id = Str::random(30);
        $existing_token = DB::table('password_resets')->where('email', $user_info->email)->delete();
        DB::table('password_resets')->insert(['email' => $user_info->email, 'token' => $token_id]);


        $message = __('Hello').' '.$user_info->username."\n";
        $message .= __('Here is you password reset link, If you did not request to reset your password just ignore this mail.') . ' <a class="btn" href="' . route('admin.reset.password', ['user' => $user_info->username, 'token' => $token_id]) . '">' . __('Click Reset Password') . '</a>';
        $subject = __('Your Mail For Reset Password Link');
        try{
            Mail::to($user_info->email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));

            return redirect()->back()->with([
                'msg' => __('Check Your Mail For Reset Password Link'),
                'type' => 'success'
            ]);
        }catch(\Exeption $e){
            //handle error
            return redirect()->back()->with([
                'msg' => $e->getMessage(),
                'type' => 'danger'
            ]);
        }
    }

    public function userLogin(Request $request)
    {
        if($request->isMethod('post')){
            $email_or_username = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|min:6'
            ],
                [
                    'username.required' => sprintf(__('%s required'),$email_or_username),
                    'password.required' => __('password required')
                ]);

            if (Auth::guard('web')->attempt([$email_or_username => $request->username, 'password' => $request->password],$request->get('remember'))){
                // check account delete status
                $user_account_status = AccountDeactivate::where('user_id', Auth::guard('web')->user()->id)
                    ->where('status', 1)
                    ->first();

                if (!empty($user_account_status) && $user_account_status->status === 1){
                    Auth::guard('web')->logout();
                    return response()->json([
                        'msg' => __('Your account has been deleted'),
                        'type' => 'danger',
                        'status' => 'account-delete'
                    ]);
                }else{
                    if(Auth::user()){
                        return response()->json([
                            'msg' => __('Login Success Redirecting'),
                            'type' => 'success',
                            'status' => 'user-login'
                        ]);
                    }
                }

            }
            return response()->json([
                'msg' => sprintf(__('Your %s or Password Is Wrong !!'),$email_or_username),
                'type' => 'danger',
                'status' => 'not_ok'
            ]);
        }
        return view('frontend.user.user-login');
    }

    public function forgetPassword(Request $request){

        if($request->isMethod('post')){
            $request->validate(['email' => 'required|email']);

            $email = User::select('email','email_verify_token')->where('email',$request->email)->first();
            if($email){
                //send otp mail
                $otp_code = sprintf("%d", random_int(123456, 999999));
                try {
                    Mail::to($email->email)->send(new BasicMail([
                        'subject' =>  __('Otp Email'),
                        'message' => __('Your otp code').' '.$otp_code
                    ]));
                }
                catch (\Exception $e) {}

                User::where('email',$request->email)->update(['email_verify_token'=>$otp_code]);

                Session::put('email',$email->email);
                return redirect()->route('user.forgot.password.otp');
            }
            return back()->with(toastr_error(__('Email not found please enter a valid email')));
        }
        return view('frontend.user.forgot-password');
    }

    public function passwordResetOtp(Request $request){

        if($request->isMethod('post')){
            $user_email = session()->get('email');

            $find_email = User::where('email',$user_email)->where('email_verify_token',$request->otp)->first();
            if($find_email){
                Session::put('user_email',$find_email->email);
                Session::put('user_otp',$request->otp);
                return redirect()->route('user.forgot.password.reset');
            }
        }
        return view('frontend.user.password-reset-otp');
    }

    public function passwordReset(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'password' => 'required|min:6|max:191',
                'confirm_password' => 'required|min:6|max:191',
            ]);
            $user_email = session()->get('user_email');
            $user_otp = session()->get('user_otp');
            $user = User::select(['email','email_verify_token'])->where('email',$user_email)->where('email_verify_token',$user_otp)->first();
            if($user){
                if ($request->password == $request->confirm_password) {
                    User::where('email',$user_email)
                        ->where('email_verify_token',$user_otp)
                        ->update(['password' => Hash::make($request->password)]);
                    return redirect()->route('user.login');
                }
                return back()->with(toastr_warning(__('Password does not match')));
            }else{
                return back()->with(toastr_warning(__('User not found')));
            }
        }
        return view('frontend.user.password-reset');
    }

    public function AdminResetPassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'username' => 'required',
            'password' => 'required|string|min:8|confirmed'
        ]);
        $user_info = Admin::where('username', $request->username)->first();
        $user = Admin::findOrFail($user_info->id);
        $token_iinfo = DB::table('password_resets')->where(['email' => $user_info->email, 'token' => $request->token])->first();
        if (!empty($token_iinfo)) {
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->route('admin.login')->with(['msg' =>__( 'Password Changed Successfully'), 'type' => 'success']);
        }
        return redirect()->back()->with(['msg' => __('Somethings Going Wrong! Please Try Again or Check Your Old Password'), 'type' => 'danger']);
    }


}
