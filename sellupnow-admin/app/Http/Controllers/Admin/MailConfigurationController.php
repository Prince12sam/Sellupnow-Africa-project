<?php

namespace App\Http\Controllers\Admin;

use App\Events\SendTestMailEvent;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MailConfigurationController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function setListoceanOption(string $key, $value): void
    {
        $this->listocean()->table('static_options')->updateOrInsert(
            ['option_name' => $key],
            ['option_value' => (string) $value]
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.mail-config');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $this->setEnv('MAIL_MAILER', $request->mailer);
            $this->setEnv('MAIL_HOST', $request->host);
            $this->setEnv('MAIL_PORT', $request->port);
            $this->setEnv('MAIL_USERNAME', $request->username);
            $this->setEnv('MAIL_PASSWORD', $request->password);
            $this->setEnv('MAIL_ENCRYPTION', $request->encryption);
            $this->setEnv('MAIL_FROM_ADDRESS', $request->from_address);

            // Also sync into Listocean (customer web) DB static_options.
            // Customer web applies SMTP settings from these keys at runtime.
            $this->setListoceanOption('site_smtp_mail_host', $request->host ?? '');
            $this->setListoceanOption('site_smtp_mail_port', $request->port ?? '');
            $this->setListoceanOption('site_smtp_mail_username', $request->username ?? '');
            $this->setListoceanOption('site_smtp_mail_password', $request->password ?? '');
            $this->setListoceanOption('site_smtp_mail_encryption', $request->encryption ?? '');
            $this->setListoceanOption('site_global_email', $request->from_address ?? '');

            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return back()->with('success', __('Mail configuration updated successfully.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function sendTestMail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'required',
        ]);

        try {
            SendTestMailEvent::dispatch($request->email, $request->message);
        } catch (\Throwable $th) {
            return back()->with('messageError', $th->getMessage());
        }

        return back()->with('success', __('Test mail sent successfully.'));
    }
}
