@extends('backend.admin-master')
@section('site-title')
    {{__('Color Settings')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/spectrum.min.css')}}">
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="col-12 mt-5">
                <div class="dashboard__card bg__white padding-20 radius-10">
                   <h2 class="dashboard__card__header__title mb-3">{{__("Color Settings")}}</h2>
                    <x-validation.error/>
                        <form action="{{route('admin.general.color.settings')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form__input__flex">
                                <div class="form__input__single">
                                    <label for="site_main_color_one"  class="form__input__single__label">{{__('Site Main Color One')}}</label>
                                    <input type="text" name="site_main_color_one" style="background-color: {{get_static_option('site_main_color_one')}};" class="form__control radius-5 spectrum_picker"
                                           value="{{get_static_option('site_main_color_one')}}" id="site_main_color_one">
                                    <br>
                                    <small class="form-text text-muted">{{__('you can change -site main color- from here, it will replace the website main color')}}</small>
                                </div>

                                <div class="fform__input__single">
                                    <label for="site_main_color_two" class="form__input__single__label">{{__('Site Main Color Two')}}</label>
                                    <input type="text" name="site_main_color_two" style="background-color: {{get_static_option('site_main_color_two')}};" class="form__control radius-5 spectrum_picker"
                                           value="{{get_static_option('site_main_color_two')}}" id="site_main_color_two">
                                    <br>
                                    <small class="form-text text-muted">{{__('you can change -site base color- from here, it will replace the website base color')}}</small>
                                </div>

                                <div class="form__input__single">
                                    <label for="site_main_color_three" class="form__input__single__label">{{__('Site Main Color Three')}}</label>
                                    <input type="text" name="site_main_color_three" style="background-color: {{get_static_option('site_main_color_three')}};" class="form__control radius-5 spectrum_picker"
                                           value="{{get_static_option('site_main_color_three')}}" id="site_main_color_three">
                                    <br>
                                    <small class="form-text text-muted">{{__('you can change -site base color- from here, it will replace the website base color')}}</small>
                                </div>

                                <div class="form__input__single">
                                    <label for="heading_color" class="form__input__single__label">{{__('Heading Color')}}</label>
                                    <input type="text" name="heading_color" style="background-color: {{get_static_option('heading_color')}};" class="form__control radius-5 spectrum_picker"
                                           value="{{get_static_option('heading_color')}}" id="heading_color">
                                    <br>
                                    <small class="form-text text-muted">{{__('you can change -heading color- from here, it will replace the website base color')}}</small>
                                </div>

                                <div class="form__input__single">
                                    <label for="light_color" class="form__input__single__label">{{__('Light Color')}}</label>
                                    <input type="text" name="light_color" style="background-color: {{get_static_option('light_color')}};" class="form__control radius-5 spectrum_picker"
                                           value="{{get_static_option('light_color')}}" id="light_color">
                                    <br>
                                    <small class="form-text text-muted">{{__('you can change -heading color- from here, it will replace the website base color')}}</small>
                                </div>

                                <div class="form__input__single">
                                    <label for="extra_light_color" class="form__input__single__label"> {{__('Extra Light Color')}}</label>
                                    <input type="text" name="extra_light_color" style="background-color: {{get_static_option('extra_light_color')}};" class="form__control radius-5 spectrum_picker"
                                           value="{{get_static_option('extra_light_color')}}" id="extra_light_color">
                                    <br>
                                    <small class="form-text text-muted">{{__('you can change -heading color- from here, it will replace the website base color')}}</small>
                                </div>
                            </div>
                            <div class="btn_wrapper mt-4">
                                <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                            </div>
                        </form>
                 </div>
           </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('assets/backend/js/spectrum.min.js')}}"></script>
    <script>
        (function($){
            "use strict";

            $(document).ready(function(){
                <x-btn.update/>
                colorPickerInit($('.spectrum_picker'))
                function colorPickerInit(selector){
                    $.each(selector,function (index,value){
                        var el = $(this);
                        el.spectrum({
                            preferredFormat: "hex",
                            showAlpha: true,
                            showPalette: true,
                            cancelText : '',
                            showInput: true,
                            allowEmpty:true,
                            chooseText : '',
                            maxSelectionSize: 2,
                            color: el.val(),
                            change: function(color) {
                                el.val( color ? color.toRgbString() : '');
                                el.css({
                                    'background-color' : color ? color.toRgbString() : ''
                                });
                            },
                            move: function(color) {
                                el.val( color ? color.toRgbString() : '');
                                el.css({
                                    'background-color' : color ? color.toRgbString() : ''
                                });
                            }
                        });

                        el.on("dragstop.spectrum", function(e, color) {
                            el.val( color.toRgbString());
                            el.css({
                                'background-color' : color.toHexString()
                            });
                        });
                    });
                }

            });
        }(jQuery));
    </script>
@endsection
