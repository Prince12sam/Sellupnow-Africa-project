<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\Wallet\app\Models\Wallet;

class SuspendActiveController extends Controller
{
    //suspend user
    public function suspend(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $user = User::where('id', $id)->first();
            User::where('id', $id)->update(['is_suspend' => 1]);

            if(moduleExists('wallet')){
                $order_balance = 0;
                $wallet_balance = Wallet::select('balance')->where('user_id', $id)->first();
                if ($wallet_balance) {
                    //if user has wallet
                    Wallet::where('user_id', $id)->update(['balance' => ($wallet_balance->balance + $order_balance)]);
                } else {
                    //if client has not wallet
                    Wallet::create([
                        'user_id' => $id,
                        'balance' => $wallet_balance ?? 0,
                        'status' => 0,
                    ]);
                }
            }
            user_notification($id, $user->id, 'Deposit', __('Your pending order price has been added to your wallet because your account has been suspended.'));
            user_notification($id, $user->id, 'Account', __('Account Suspend'));

            //Email to user according to their id
            try {
                $message = get_static_option('account_suspend_message') ?? __('Account Suspend Message');
                $message = str_replace(["@name"], [$user->fullname], $message);
                Mail::to($user->email)->send(new BasicMail([
                    'subject' => get_static_option('account_suspend_subject') ?? __('Account Suspend Email'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {
            }
            return back()->with(FlashMsg::item_new(__('Account Successfully Suspended.')));
        }

        $user_wallet_balance = Wallet::where('user_id', $id)->first();
        $user = User::where('user_id', $id)->first();
        return view('backend.pages.account.suspend', compact(['user_wallet_balance', 'user']));
    }

    //unsuspend user
    public function unsuspend($id)
    {
        $user = User::find($id);
        User::where('id', $id)->update(['is_suspend' => 0]);
        user_notification($id, $user->id, 'Account', __('Account Unsuspended'));

        //Email to user according to their id
        try {
            $message = get_static_option('account_unsuspend_message') ?? __('Account Unsuspend Message');
            $message = str_replace(["@name"], [$user->fullname], $message);
            Mail::to($user->email)->send(new BasicMail([
                'subject' => get_static_option('account_unsuspend_subject') ?? __('Account Unsuspend Email'),
                'message' => $message
            ]));
        } catch (\Exception $e) {
        }
        return back()->with(FlashMsg::item_new(__('Account Successfully Unsuspended.')));
    }
}
