<?php

namespace Modules\PaymentGateways\app\Http\Controllers;

use App\Helpers\ModuleMetaData;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Modules\PaymentGateways\app\Models\PaymentGateway;

class PaymentGatewaysController extends Controller
{
    const BASE_PATH = 'paymentgateways::backend.';

    public function paymentGatewaySettings()
    {
        return view(self::BASE_PATH . 'currency-settings');
    }

    public function paymentGatewaySettingsUpdate(Request $request)
    {
        $field_rules = [
            // site global
            'site_global_currency' => 'nullable|string|max:191',
            'site_global_payment_gateway' => 'nullable|string|max:191',
            // site manual
            'site_manual_payment_name' => 'nullable|string|max:191',
            'site_manual_payment_description' => 'nullable|string|max:191',
            // exchange rate
            'site_usd_to_ngn_exchange_rate' => 'nullable|string|max:191',
            'site_euro_to_ngn_exchange_rate' => 'nullable|string|max:191',
            'site_currency_symbol_position' => 'nullable|string|max:191',
            'site_default_payment_gateway' => 'nullable|string|max:191',
        ];

        $saveAllPaymentGatewaySettings = (new ModuleMetaData())->saveAllPaymentGatewaySettings();
        foreach ($saveAllPaymentGatewaySettings as $pay_settings){
            foreach ($pay_settings as $pset){
                if (empty($pset)){continue;}
                update_static_option($pset, $request->$pset);
            }
        }

        $this->validate($request, $field_rules);
        $global_currency = get_static_option('site_global_currency');
        $field_rules['site_' . strtolower($global_currency) . '_to_idr_exchange_rate'] = 0;
        $field_rules['site_' . strtolower($global_currency) . '_to_inr_exchange_rate'] = 0;
        $field_rules['site_' . strtolower($global_currency) . '_to_ngn_exchange_rate'] = 0;
        $field_rules['site_' . strtolower($global_currency) . '_to_zar_exchange_rate'] = 0;
        $field_rules['site_' . strtolower($global_currency) . '_to_brl_exchange_rate'] = 0;
        $field_rules['site_' . strtolower($global_currency) . '_to_myr_exchange_rate'] = 0;

        foreach ($field_rules as $item => $rule) {
            update_static_option($item, $request->$item);
        }

        //update modules payment gateway settings data
        update_static_option('add_remove_comman_form_amount',$request->add_remove_comman_form_amount);
        update_static_option('enable_disable_decimal_point',$request->enable_disable_decimal_point);
        update_static_option('add_remove_sapce_between_amount_and_symbol',$request->add_remove_sapce_between_amount_and_symbol);
        $global_currency = get_static_option('site_global_currency');
        $currency_filed_name = 'site_' . strtolower($global_currency) . '_to_usd_exchange_rate';
        update_static_option('site_' . strtolower($global_currency) . '_to_usd_exchange_rate', $request->$currency_filed_name);
        $idr_currency_filed_name = 'site_' . strtolower($global_currency) . '_to_idr_exchange_rate';
        $inr_currency_filed_name = 'site_' . strtolower($global_currency) . '_to_inr_exchange_rate';
        $ngn_currency_filed_name = 'site_' . strtolower($global_currency) . '_to_ngn_exchange_rate';
        $zar_currency_filed_name = 'site_' . strtolower($global_currency) . '_to_zar_exchange_rate';
        $brl_currency_filed_name = 'site_' . strtolower($global_currency) . '_to_brl_exchange_rate';
        $myr_exchange_rate = 'site_' . strtolower($global_currency) . '_to_myr_exchange_rate';

        return redirect()->back()->with([
            'msg' => __('Payment Settings Updated..'),
            'type' => 'success'
        ]);
    }
    public function paypal_settings()
    {
        $gateway = PaymentGateway::where('name', 'paypal')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function paytm_settings()
    {
        $gateway = PaymentGateway::where('name', 'paytm')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function stripe_settings()
    {
        $gateway = PaymentGateway::where('name', 'stripe')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function razorpay_settings()
    {
        $gateway = PaymentGateway::where('name', 'razorpay')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function paystack_settings()
    {
        $gateway = PaymentGateway::where('name', 'paystack')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function mollie_settings()
    {
        $gateway = PaymentGateway::where('name', 'mollie')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function midtrans_settings()
    {
        $gateway = PaymentGateway::where('name', 'midtrans')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function cashfree_settings()
    {
        $gateway = PaymentGateway::where('name', 'cashfree')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function instamojo_settings()
    {
        $gateway = PaymentGateway::where('name', 'instamojo')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function marcadopago_settings()
    {
        $gateway = PaymentGateway::where('name', 'marcadopago')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function zitopay_settings()
    {
        $gateway = PaymentGateway::where('name', 'zitopay')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function squareup_settings()
    {
        $gateway = PaymentGateway::where('name', 'squareup')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function cinetpay_settings()
    {
        $gateway = PaymentGateway::where('name', 'cinetpay')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function paytabs_settings()
    {
        $gateway = PaymentGateway::where('name', 'paytabs')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function billplz_settings()
    {
        $gateway = PaymentGateway::where('name', 'billplz')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function toyyibpay_settings()
    {
        $gateway = PaymentGateway::where('name', 'toyyibpay')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function flutterwave_settings()
    {
        $gateway = PaymentGateway::where('name', 'flutterwave')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function payfast_settings()
    {
        $gateway = PaymentGateway::where('name', 'payfast')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function manual_payment_settings()
    {
        $gateway = PaymentGateway::where('name', 'manual_payment')->first();
        return view(self::BASE_PATH . 'payment_settings', compact('gateway'));
    }

    public function cod_settings()
    {
        $gateway = PaymentGateway::where('name', 'manual_payment')->first();
        $cod = true;
        return view(self::BASE_PATH . 'payment_settings', compact('gateway', 'cod'));
    }

    public function update_payment_settings(Request $request)
    {
        $request->validate([
            'gateway_name' => 'required'
        ]);

        if ($request->gateway_name == 'cash_on_delivery')
        {
            update_static_option('cash_on_delivery', $request->cash_on_delivery);
        } else {
            $gateway = PaymentGateway::where('name', $request->gateway_name)->first();

            // if manual payament gatewya then save description into database
            $image_name = $gateway->name . '_logo';
            $status_name = $gateway->name . '_gateway';
            $test_mode_name = $gateway->name . '_test_mode';
            $credentials = !empty($gateway->credentials) ? json_decode($gateway->credentials) : [];
            $update_credentials = [];
            foreach ($credentials as $cred_name => $cred_val) {
                $crd_req_name = $gateway->name . '_' . $cred_name;
                $update_credentials[$cred_name] = $request->$crd_req_name;
            }

            PaymentGateway::where(['name' => $gateway->name])->update([
                'image' => $request->$image_name,
                'status' => isset($request->$status_name) ? 1 : 0,
                'test_mode' => isset($request->$test_mode_name) ? 1 : 0,
                'credentials' => json_encode($update_credentials)
            ]);
        }

        Artisan::call('optimize:clear');
        return redirect()->back()->with([
            'msg' => __('Payment Settings Updated..'),
            'type' => 'success'
        ]);
    }
}
