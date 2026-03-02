<?php

namespace App\Jobs;

use App\Mail\BasicMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRegisterUserEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $user_request_password;
    public function __construct($user, $user_request_password)
    {
        $this->user = $user;
        $this->user_request_password = $user_request_password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //send register mail to admin
        try {
            $message = get_static_option('user_register_message_for_admin') ?? __('Hello Admin a new user just have registered');
            $message = str_replace(["@name","@email","@username"],[$this->user->first_name.' '.$this->user->last_name, $this->user->email, $this->user->username], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => get_static_option('user_register_subject') ?? __('New User Register Email'),
                'message' => $message
            ]));

        }
        catch (\Exception $e) {}

        //send register mail to user
        try {
            $message = get_static_option('user_register_message') ?? __('Your registration successfully completed.');
            $message = str_replace(["@name","@email","@username","@password"],[$this->user->first_name.' '.$this->user->last_name, $this->user->email, $this->user->username, $this->user_request_password], $message);
            Mail::to($this->user->email)->send(new BasicMail([
                'subject' => get_static_option('user_register_subject') ?? __('User Register Welcome Email'),
                'message' => $message
            ]));
        }
        catch (\Exception $e) {}
    }
}
