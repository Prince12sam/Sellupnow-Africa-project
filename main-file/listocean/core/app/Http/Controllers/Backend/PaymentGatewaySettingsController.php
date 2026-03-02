<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ModuleMetaData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentGatewaySettingsController extends Controller
{
    public function paymentGatewaySettings()
    {
        return view('backend.settings.payment-gateway.currency-settings');
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
            // manual payment
            'manual_payment_preview_logo' => 'nullable|string|max:191',
            'manual_payment_gateway' => 'nullable|string|max:191',
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

        // update modules payment gateway settings data
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

}
