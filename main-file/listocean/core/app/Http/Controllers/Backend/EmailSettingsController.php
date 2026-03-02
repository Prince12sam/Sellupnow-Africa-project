<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailSettingsController extends Controller
{

    public function updateEmailSettings(Request $request)
    {
        $all_languages = Language::all();
        foreach ($all_languages as $lang) {
            $this->validate($request, [
                'contact_mail_success_message' => 'nullable|string',
                'order_mail_success_message' => 'nullable|string',

            ]);
            $fields = [
                'contact_mail_success_message',
                'order_mail_success_message',
            ];
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    update_static_option($field, $request->$field);
                }
            }
        }
        return redirect()->back()->with(FlashMsg::settings_update());
    }

    public function smtpSettings()
    {
        return view('backend.email-settings.smtp-settings');
    }

    public function updateSmtpSettings(Request $request){
        $this->validate($request,[
            'site_global_email' => 'required|string',
            'site_smtp_mail_host' => 'required|string',
            'site_smtp_mail_port' => 'required|string',
            'site_smtp_mail_username' => 'required|string',
            'site_smtp_mail_password' => 'required|string',
            'site_smtp_mail_encryption' => 'required|string'
        ]);

        update_static_option('site_global_email',$request->site_global_email);
        update_static_option('site_smtp_mail_mailer',$request->site_smtp_mail_mailer);
        update_static_option('site_smtp_mail_host',$request->site_smtp_mail_host);
        update_static_option('site_smtp_mail_port',$request->site_smtp_mail_port);
        update_static_option('site_smtp_mail_username',$request->site_smtp_mail_username);
        update_static_option('site_smtp_mail_password',$request->site_smtp_mail_password);
        update_static_option('site_smtp_mail_encryption',$request->site_smtp_mail_encryption);

        // for email
        setEnvValue([
            'MAIL_DRIVER' => $request->site_smtp_mail_mailer,
            'MAIL_HOST' => $request->site_smtp_mail_host,
            'MAIL_PORT' => $request->site_smtp_mail_port,
            'MAIL_USERNAME' => $request->site_smtp_mail_username,
            'MAIL_PASSWORD' => '"'.$request->site_smtp_mail_password.'"',
            'MAIL_ENCRYPTION' => $request->site_smtp_mail_encryption
        ]);

        return redirect()->back()->with(['msg' => __('SMTP Settings Updated...'),'type' => 'success']);
    }


    public function testSmtpSettings(Request $request){
        $this->validate($request,[
            'email' => 'required|email|max:191'
        ]);
        $res_data = [
            'msg' => __('Mail Sent Success'),
            'type' => 'success'
        ];
        $subject = __('Default Test Subject');
        $message = __('Default Test Message');
        try {
            Mail::to($request->email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));
        }catch (\Exception $e){
            return  redirect()->back()->with([
                'type' => 'danger',
                'msg' => $e->getMessage()
            ]);
        }

        if (Mail::flushMacros()){
            $res_data = [
                'msg' => __('Mail Sent Failed'),
                'type' => 'danger'
            ];
        }

        return redirect()->back()->with($res_data);
    }

}
