@extends('backend.admin-master')
@section('site-title')
    {{__('Payment Gateway')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Payment Gateway')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.payment.settings.update')}}" method="POST" enctype="multipart/form-data">
                     @csrf
                        @if(!empty($gateway->description))
                            <div class="payment-notice alert alert-warning">
                                <p>{{$gateway->description}}</p>
                            </div>
                        @endif
                        @if(isset($cod) && $cod)
                            <input type="hidden" name="gateway_name" value="cash_on_delivery">
                           <x-fields.switcher value="{{get_static_option('cash_on_delivery')}}" name="cash_on_delivery"   label="{{__('Enable Cash On Delivery')}}"/>
                        @else
                            <input type="hidden" name="gateway_name" value="{{$gateway->name}}">
                            <div class="form__input__single">
                                <label  for="instamojo_gateway"><strong>{{__('Enable/Disable '. ucfirst($gateway->name))}}</strong></label>
                                <input type="hidden" name="{{$gateway->name}}_gateway">
                                <div class="switch_box style_7">
                                    <input type="checkbox" name="{{$gateway->name}}_gateway"  @if($gateway->status === 1 ) checked @endif>
                                    <label></label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="instamojo_test_mode"><strong>{{__("Enable Test Mode"." ".ucfirst($gateway->name))}}</strong></label>
                                <div class="switch_box style_7">
                                    <input type="checkbox" name="{{$gateway->name}}_test_mode"  @if($gateway->test_mode === 1 ) checked @endif>
                                    <label></label>
                                </div>
                            </div>

                            <x-media.edit-media-upload-image
                                label="{{ __(ucfirst($gateway->name).' '.'Logo') }}"
                                name="{{$gateway->name.'_logo'}}" :value="$gateway->image"/>
                            @php
                                $credentials = !empty($gateway->credentials) ? json_decode($gateway->credentials) : [];
                            @endphp
                            @foreach($credentials as $cre_name =>  $cre_value)
                                <div class="form-group">
                                    <label>{{ str_replace('_', ' ' , ucwords($cre_name)) }}</label>
                                    <input type="text" name="{{$gateway->name.'_'.$cre_name}}"
                                           value="{{$cre_value}}"
                                           class="form-control">
                                    @if($gateway->name == 'paytabs')
                                        @if($cre_name == 'region')
                                            <small class="text-secondary" style="font-size: 13px">GLOBAL,
                                                ARE, EGY, SAU,
                                                OMN, JOR</small>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endif
                     <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5 mt-3">{{__('Update Changes')}}</button>
                </form>
            </div>
        </div>
   </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function ($) {
                $('.summernote').summernote({
                    height: 200,
                    codemirror: {
                        theme: 'monokai'
                    },
                    callbacks: {
                        onChange: function (contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
                if ($('.summernote').length > 0) {
                    $('.summernote').each(function (index, value) {
                        $(this).summernote('code', $(this).data('content'));
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
