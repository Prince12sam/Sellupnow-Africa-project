@extends('backend.admin-master')
@section('site-title')
    {{__('Currency Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Currency Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.payment.gateway.currency.settings')}}" method="POST">
                    @csrf
                    <div class="form__input__flex">

                        <div class="form__input__single">
                            <label class="form__input__single__label" for="site_canonical_url_type" class="form__input__single__label">{{__('Site Global Currency')}}</label>
                            <select class="form__control radius-5" name="site_global_currency" id="site_global_currency">
                                @foreach(Xgenious\Paymentgateway\Facades\XgPaymentGateway::script_currency_list() as $cur => $symbol)
                                    <option value="{{$cur}}" @if(get_static_option('site_global_currency') == $cur) selected @endif >{{$cur.' ( '.$symbol.' )'}} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label" for="site_title" class="form__input__single__label">{{ __('Site Title') }}</label>
                            <input type="text"  name="site_title" id="site_title" value="{{get_static_option('site_title')}}"  class="form__control radius-5">
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label" for="site_global_currency">{{__('Enable/Disable Decimal Point')}}</label>
                            <select name="enable_disable_decimal_point" class="form__control radius-5" id="enable_disable_decimal_point">
                                <option value="yes" {{get_static_option('enable_disable_decimal_point') == 'yes' ? 'selected' : ''}}>{{ __('Yes') }}</option>
                                <option value="no" {{get_static_option('enable_disable_decimal_point') == 'no' ? 'selected' : ''}}>{{ __('No') }}</option>
                            </select>
                        </div>
                        <div class="form__input__single">
                            <label class="form__input__single__label" for="site_global_currency">{{__('Add/Remove Space Between Currency Symbol and Amount')}}</label>
                            <select name="add_remove_sapce_between_amount_and_symbol" class="form__control radius-5">
                                <option value="yes" {{get_static_option('add_remove_sapce_between_amount_and_symbol') == 'yes' ? 'selected' : ''}}>{{ __('Yes') }}</option>
                                <option value="no" {{get_static_option('add_remove_sapce_between_amount_and_symbol') == 'no' ? 'selected' : ''}}>{{ __('No') }}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label" for="add_remove_comman_form_amount">{{__('Add/Remove comma (,) from amount')}}</label>
                            <select name="add_remove_comman_form_amount" class="form__control radius-5">
                                <option value="yes" {{get_static_option('add_remove_comman_form_amount') == 'yes' ? 'selected' : ''}}>{{ __('Yes') }}</option>
                                <option value="no" {{get_static_option('add_remove_comman_form_amount') == 'no' ? 'selected' : ''}}>{{ __('No') }}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label" for="site_currency_symbol_position">{{__('Currency Symbol Position')}}</label>
                            @php $all_currency_position = ['left','right']; @endphp
                            <select name="site_currency_symbol_position" class="form__control radius-5"
                                    id="site_currency_symbol_position">
                                @foreach($all_currency_position as $cur)
                                    <option value="{{$cur}}"
                                            @if(get_static_option('site_currency_symbol_position') == $cur) selected @endif>{{ucwords($cur)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form__input__single">
                            @php
                                $all_gateways = \App\Helpers\PaymentGatewayRenderHelper::listOfPaymentGateways();
                            @endphp

                            <label class="form__input__single__label" for="site_default_payment_gateway">{{__('Default Payment Gateway')}}</label>
                            <select name="site_default_payment_gateway" class="form__control radius-5" >
                                @foreach($all_gateways as $gateway)
                                    <option value="{{$gateway['name']}}" @if(get_static_option('site_default_payment_gateway') == $gateway['name']) selected @endif>{{ucwords(str_replace('_',' ',$gateway['name']))}}</option>
                                @endforeach
                            </select>
                        </div>
                        @php $global_currency = get_static_option('site_global_currency');@endphp

                        @if($global_currency != 'USD')
                            <div class="form__input__single">
                                <label class="form__input__single__label" for="site_{{strtolower($global_currency)}}_to_usd_exchange_rate">{{__($global_currency.' to USD Exchange Rate')}}</label>
                                <input type="text" class="form__control radius-5"
                                       name="site_{{strtolower($global_currency)}}_to_usd_exchange_rate"
                                       value="{{get_static_option('site_'.$global_currency.'_to_usd_exchange_rate')}}">
                                <span class="info-text">{{sprintf(__('enter %s to USD exchange rate. eg: 1 %s = ? USD'),$global_currency,$global_currency) }}</span>
                            </div>
                        @endif

                        @if($global_currency != 'IDR')
                            <div class="form__input__single">
                                <label class="form__input__single__label" for="site_{{strtolower($global_currency)}}_to_idr_exchange_rate">{{__($global_currency.' to IDR Exchange Rate')}}</label>
                                <input type="text" class="form__control radius-5"
                                       name="site_{{strtolower($global_currency)}}_to_idr_exchange_rate"
                                       value="{{get_static_option('site_'.$global_currency.'_to_idr_exchange_rate')}}">
                                <span class="info-text">{{sprintf(__('enter %s to USD exchange rate. eg: 1 %s = ? IDR'),$global_currency,$global_currency) }}</span>
                            </div>
                        @endif

                        @if($global_currency != 'INR' && !empty(get_static_option('paytm_gateway') || !empty(get_static_option('razorpay_gateway'))))
                            <div class="form__input__single">
                                <label class="form__input__single__label" for="site_{{strtolower($global_currency)}}_to_inr_exchange_rate">{{__($global_currency.' to INR Exchange Rate')}}</label>
                                <input type="text" class="form__control radius-5"
                                       name="site_{{strtolower($global_currency)}}_to_inr_exchange_rate"
                                       value="{{get_static_option('site_'.$global_currency.'_to_inr_exchange_rate')}}">
                                <span class="info-text">{{__('enter '.$global_currency.' to INR exchange rate. eg: 1'.$global_currency.' = ? INR')}}</span>
                            </div>
                        @endif

                        @if($global_currency != 'NGN' && !empty(get_static_option('paystack_gateway') ))
                            <div class="form__input__single">
                                <label class="form__input__single__label" for="site_{{strtolower($global_currency)}}_to_ngn_exchange_rate">{{__($global_currency.' to NGN Exchange Rate')}}</label>
                                <input type="text" class="form__control radius-5"
                                       name="site_{{strtolower($global_currency)}}_to_ngn_exchange_rate"
                                       value="{{get_static_option('site_'.$global_currency.'_to_ngn_exchange_rate')}}">
                                <span class="info-text">{{__('enter '.$global_currency.' to NGN exchange rate. eg: 1'.$global_currency.' = ? NGN')}}</span>
                            </div>
                        @endif

                        @if($global_currency != 'ZAR')
                            <div class="form__input__single">
                                <label class="form__input__single__label" for="site_{{strtolower($global_currency)}}_to_zar_exchange_rate">{{__($global_currency.' to ZAR Exchange Rate')}}</label>
                                <input type="text" class="form__control radius-5"  name="site_{{strtolower($global_currency)}}_to_zar_exchange_rate"  value="{{get_static_option('site_'.$global_currency.'_to_zar_exchange_rate')}}">
                                <span class="info-text">{{sprintf(__('enter %s to USD exchange rate. eg: 1 %s = ? ZAR'),$global_currency,$global_currency) }}</span>
                            </div>
                        @endif

                        @if($global_currency != 'BRL')
                            <div class="form__input__single">
                                <label class="form__input__single__label" for="site_{{strtolower($global_currency)}}_to_brl_exchange_rate">{{__($global_currency.' to BRL Exchange Rate')}}</label>
                                <input type="text" class="form__control radius-5"
                                       name="site_{{strtolower($global_currency)}}_to_brl_exchange_rate"
                                       value="{{get_static_option('site_'.$global_currency.'_to_brl_exchange_rate')}}">
                                <span class="info-text">{{__('enter '.$global_currency.' to BRL exchange rate. eg: 1'.$global_currency.' = ? BRL')}}</span>
                            </div>
                        @endif
                        @if($global_currency != 'MYR')
                            <div class="form__input__single">
                                <label class="form__input__single__label" for="site_{{strtolower($global_currency)}}_to_myr_exchange_rate">{{__($global_currency.' to MYR Exchange Rate')}}</label>
                                <input type="text" class="form__control radius-5"
                                       name="site_{{strtolower($global_currency)}}_to_myr_exchange_rate"
                                       value="{{get_static_option('site_'.$global_currency.'_to_myr_exchange_rate')}}">
                                <span class="info-text">{{__('enter '.$global_currency.' to MYR exchange rate. eg: 1'.$global_currency.' = ? MYR')}}</span>
                            </div>
                        @endif
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
