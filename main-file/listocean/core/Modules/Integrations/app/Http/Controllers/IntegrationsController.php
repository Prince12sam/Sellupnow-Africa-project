<?php

namespace Modules\Integrations\app\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IntegrationsController extends Controller
{
    public function store(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'data_type' => 'required',
            ]);

            match ($request->data_type){
                "google_analytics" => $this->google_analytics(),
                "google_tag_manager" => $this->google_tag_manager(),
                "facebook_pixels" => $this->facebook_pixels(),
                "adroll_pixels" => $this->adroll_pixels(),
                "whatsapp" => $this->whatsapp(),
                "twakto" => $this->twakto(),
                "crsip" => $this->crsip(),
                "tidio" => $this->tidio(),
                "messenger" => $this->messenger(),
                "instagram" => $this->instagram(),
                "google_captcha_v3" => $this->google_captcha_v3(),
                "google_adsense" => $this->google_adsense(),
                "social_login" => $this->social_login(),
            };
            return back()->with(toastr_success(__('Settings updated')));
        }
        return view("integrations::integrations.index");

    }

    private function google_analytics(){
        $req = \request();
        update_static_option('google_analytics_gt4_ID',$req->google_analytics_gt4_ID);
    }

    private function google_captcha_v3()
    {
        $req = \request();

        update_static_option('site_google_captcha_v3_site_key',$req->site_google_captcha_v3_site_key);
        update_static_option('site_google_captcha_v3_secret_key',$req->site_google_captcha_v3_secret_key);
    }

    public function activate(Request $request){
        $request->validate([
            'status' => 'nullable',
            'option_name' => "required"
        ]);

        update_static_option($request->option_name,$request->status === 'on' ? 'off' : 'on');
        return response()->json(['msg' => __('Settings Updated'),'type' => 'success','status'=>$request->status]);
    }

    private function google_tag_manager()
    {
        $req = \request();
        update_static_option('google_tag_manager_ID',$req->google_tag_manager_ID);
    }

    private function facebook_pixels()
    {
        $req = \request();
        update_static_option('facebook_pixels_id',$req->facebook_pixels_id);
    }

    private function adroll_pixels()
    {
        $req = \request();
        update_static_option('adroll_adviser_id',$req->adroll_adviser_id);
        update_static_option('adroll_publisher_id',$req->adroll_publisher_id);
    }

    private function whatsapp()
    {
        $req = \request();
        update_static_option('whatsapp_mobile_number',$req->whatsapp_mobile_number);
        update_static_option('whatsapp_initial_text',$req->whatsapp_initial_text);

    }

    private function twakto()
    {
        $req = \request();
        update_static_option('twakto_widget_id',$req->twakto_widget_id);
    }

    private function crsip()
    {
        $req = \request();
        update_static_option('crsip_website_id',$req->crsip_website_id);
    }

    private function tidio()
    {
        $req = \request();
        update_static_option('tidio_chat_page_id',$req->tidio_chat_page_id);
    }

    private function messenger()
    {
        $req = \request();
        update_static_option('messenger_page_id',$req->messenger_page_id);
    }

    private function instagram()
    {
        $req = \request();
        update_static_option('instagram_access_token',$req->instagram_access_token);
    }
    private function google_adsense()
    {
        $req = \request();
        update_static_option('google_adsense_publisher_id',$req->google_adsense_publisher_id);
        update_static_option('google_adsense_customer_id',$req->google_adsense_customer_id);
    }

    private function social_login()
    {
        $req = \request();
        update_static_option('enable_facebook_login',$req->enable_facebook_login);
        update_static_option('facebook_client_id',$req->facebook_client_id);
        update_static_option('facebook_client_secret',$req->facebook_client_secret);
        update_static_option('facebook_callback_url',$req->facebook_callback_url);
        update_static_option('enable_google_login',$req->enable_google_login);
        update_static_option('google_client_id',$req->google_client_id);
        update_static_option('google_client_secret',$req->google_client_secret);
        update_static_option('google_callback_url',$req->google_callback_url);

        setEnvValue([
            'GOOGLE_ADSENSE_PUBLISHER_ID' => $req->google_adsense_publisher_id,
            'GOOGLE_ADSENSE_CUSTOMER_ID' => $req->google_adsense_customer_id,
            'FACEBOOK_CLIENT_ID' => $req->facebook_client_id,
            'FACEBOOK_CLIENT_SECRET' => $req->facebook_client_secret,
            'FACEBOOK_CALLBACK_URL' => route('facebook.callback'),
            'GOOGLE_CLIENT_ID' => $req->google_client_id,
            'GOOGLE_CLIENT_SECRET' => $req->google_client_secret,
            'GOOGLE_CALLBACK_URL' => route('google.callback'),
        ]);
    }
}
