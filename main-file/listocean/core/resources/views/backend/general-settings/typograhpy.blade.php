@extends('backend.admin-master')
@section('site-title')
    {{__('Typography Settings')}}
@endsection
@section('style')
    <x-datatable.js/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/codemirror.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/show-hint.css')}}">
    <style>
        .dashboard__inner__item__header__title{
            font-size: 16px;
        }
        .select2-container {
            border: 1px solid #dfdfdf;
        }
        div#DataTables_Table_0_filter {
            display: flex;
            justify-content: end;
            padding-bottom: 16px;
        }
    </style>
@endsection
@section('content')

    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Typography Settings')}}</h2>
                <x-validation.error/>
                <div class="form__input__single custom_font_title_button">
                    <label for="custom_font" class="form__input__single__label"><strong>{{__('Use Custom Font')}}</strong></label> <br>
                    <label class="switch_box style_7">
                        <input type="checkbox" name="custom_font" @if($custom_font >= 1) checked @endif id="custom_font">
                        <label></label>
                    </label>
                </div>
                <!-- custom font -->
                  <div class="custom_font_upload">
                      <h4 class="dashboard__card__header__title mt-4">{{ __('Upload Font') }}</h4>
                    <form action="{{ route('admin.custom.font.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="files[]" placeholder="{{__('Choose files')}}" multiple>
                        <button type="submit"  class="cmnBtn btn_5 btn_bg_secondary radius-5">{{__('Upload')}}</button>
                    </form>
                      <span class="text-danger">{{ __('allowed file format: ttf, woff, woff2, eot')  }}</span>
                  </div>

                    <div class="row custom_font_hide_and_show mt-5">
                        <div class="col-xl-12 col-lg-12">
                            <h4 class="dashboard__inner__item__header__title mt-2">{{__('Custom Font Import')}}</h4>
                            <div class="tableStyle_three">
                                <div class="table_wrapper custom_dataTable">
                                    <table class="dataTablesExample">
                                        <thead>
                                            <th>{{__('ID')}}</th>
                                            <th>{{__('Font Family')}}</th>
                                            <th>{{__('Status')}}</th>
                                            <th>{{__('Body Typography')}}</th>
                                            <th>{{__('Heading Typography')}}</th>
                                            <th>{{__('Action')}}</th>
                                        </thead>
                                        <tbody>
                                        @foreach($all_fonts as $data)
                                            <tr>
                                                <td>{{$data->id}}</td>
                                                <td>{{$data->file}}</td>
                                                <td>
                                                    @if($data->status==1)
                                                        <span class="alert alert-success">{{__('Active')}}</span>
                                                    @elseif($data->status==2)
                                                            <span class="alert alert-success">{{__('Active')}}</span>
                                                    @else
                                                        <span class="alert alert-danger">{{__('Inactive')}}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($data->status==1)
                                                        <i class="las la-check-circle" style="font-size: 32px;color: #28a745"></i>
                                                    @endif
                                                    @if($data->status==0)
                                                            <span><x-status.custom-body-font :url="route('admin.custom.font.status',$data->id)"/></span>
                                                    @endif

                                                </td>
                                                <td>
                                                    @if($data->status==2)
                                                        <i class="las la-check-circle" style="font-size: 32px;color: #28a745"></i>
                                                    @endif
                                                    @if($data->status==0)
                                                    <span><x-status.custom-heading-font :url="route('admin.custom.heading.font.status',$data->id)"/></span>
                                                    @endif

                                                </td>
                                                <td>
                                                    @if($data->status == 0)
                                                        <x-popup.delete-popup :url="route('admin.custom.delete.font.file',$data->id)"/>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  <!-- custom font end -->

                <div class="google_font_hide_and_show">
                    <h4 class="dashboard__inner__item__header__title mt-4">{{__("Body Typography Settings")}}</h4>
                        <form action="{{route('admin.general.typography.settings')}}" method="POST" enctype="multipart/form-data" id="typographyFormSubmit">
                            @csrf
                            <div class="form__input__single">
                                <label for="body_font_family" class="form__input__single__label">{{__('Font Family')}}</label>

                                <select class="select2_activation wide" name="body_font_family" id="body_font_family">
                                    <option>{{ __('Select') }}</option>
                                    @foreach($google_fonts as $font_family => $font_variant)
                                        <option value="{{$font_family}}" @if($font_family == get_static_option('body_font_family')) selected @endif>{{$font_family}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form__input__single">
                                <label for="body_font_variant" class="form__input__single__label">{{__('Font Variant')}}</label>
                                @php
                                    $font_family_selected = get_static_option('body_font_family') ?? get_static_option('body_font_family') ;
                                    $get_font_family_variants = property_exists($google_fonts,$font_family_selected) ? (array) $google_fonts->$font_family_selected : ['variants' => array('regular')];
                                @endphp
                                <select class="select2_activation wide" id="body_font_variant" name="body_font_variant[]" multiple="multiple">
                                    <option>{{ __('Select') }}</option>
                                    @foreach($get_font_family_variants['variants'] as $variant)
                                        @php
                                            $selected_variant = !empty(get_static_option('body_font_variant')) ? unserialize(get_static_option('body_font_variant')) : [];
                                        @endphp
                                        <option value="{{$variant}}" @if(in_array($variant,$selected_variant)) selected @endif>{{str_replace(['0,','1,'],['','i'],$variant)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <h4 class="dashboard__inner__item__header__title mt-4">{{__('Heading Typography Settings')}}</h4>

                            <div class="form__input__single">
                                <label for="heading_font" class="form__input__single__label">{{__('Heading Font')}}</label> <br>
                                <label class="switch_box style_7">
                                    <input type="checkbox" name="heading_font"  @if(!empty(get_static_option('heading_font'))) checked @endif id="heading_font">
                                    <label></label>
                                </label>
                                <small>{{__('Use different font family for heading tags ( h1,h2,h3,h4,h5,h6)')}}</small>
                            </div>

                            <div class="form__input__single">
                                <label for="heading_font_family" class="form__input__single__label">{{__('Font Family')}}</label>
                                <select class="select2_activation wide" name="heading_font_family" id="heading_font_family">
                                    <option>{{ __('Select') }}</option>
                                    @foreach($google_fonts as $font_family => $font_variant)
                                        <option value="{{$font_family}}" @if($font_family == get_static_option('heading_font_family')) selected @endif>{{$font_family}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form__input__single">
                                <label for="heading_font_variant" class="form__input__single__label">{{__('Font Variant')}}</label>
                                @php
                                    $font_family_selected = get_static_option('heading_font_family') ?? '';
                                    $get_font_family_variants = property_exists($google_fonts,$font_family_selected) ? (array) $google_fonts->$font_family_selected : ['variants' => array('regular')];
                                @endphp
                                <select class="select2_activation wide" name="heading_font_variant[]" id="heading_font_variant" multiple="multiple">
                                    <option>{{ __('Select') }}</option>
                                    @foreach($get_font_family_variants['variants'] as $variant)
                                        @php
                                            $selected_variant = !empty(get_static_option('heading_font_variant')) ? unserialize(get_static_option('heading_font_variant')) : [];
                                        @endphp
                                        <option value="{{$variant}}" @if(in_array($variant,$selected_variant)) selected @endif>{{str_replace(['0,','1,'],['','i'],$variant)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="btn_wrapper mt-4">
                                <button type="submit" id="typography_submit_btn" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                            </div>
                        </form>
               </div>
             </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-datatable.js/>
    <script src="{{asset('assets/backend/js/codemirror.js')}}"></script>
    <script src="{{asset('assets/backend/js/css.js')}}"></script>
    <script src="{{asset('assets/backend/js/show-hint.js')}}"></script>
    <script src="{{asset('assets/backend/js/css-hint.js')}}"></script>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){

                //load font variant Four
                $(document).on('change','#body_font_family_three',function (e) {
                    e.preventDefault();
                    var fontFamily =  $(this).val();
                    getVariant($(this).val(),'body_font_variant_three');
                });

                //load font variant Four
                $(document).on('change','#body_font_family_four',function (e) {
                    e.preventDefault();
                    var fontFamily =  $(this).val();
                    getVariant($(this).val(),'body_font_variant_four');
                });

                //load font variant Five
                $(document).on('change','#body_font_family_five',function (e) {
                    e.preventDefault();
                    var fontFamily =  $(this).val();
                    getVariant($(this).val(),'body_font_variant_five');
                });

                function getVariant(fontFamily,selector){
                    $.ajax({
                        url: "{{route('admin.general.typography.single')}}",
                        type: "POST",
                        data:{
                            _token: "{{csrf_token()}}",
                            font_family : fontFamily
                        },
                        success:function (data) {
                            var variantSelector = $('#'+selector);
                            variantSelector.html('');
                            $.each(data.variants,function (index,value) {
                                var nameval = value.replace('0,','');
                                nameval = nameval.replace('1,','i');
                                variantSelector.append('<option value="'+value+'">'+nameval+'</option>');
                            });
                            variantSelector.niceSelect('update');
                        }
                    });
                }


                $(document).on('change','#body_font_family',function (e) {
                    e.preventDefault();
                    var fontFamily =  $(this).val();

                    $.ajax({
                        url: "{{route('admin.general.typography.single')}}",
                        type: "POST",
                        data:{
                            _token: "{{csrf_token()}}",
                            font_family : fontFamily
                        },
                        success:function (data) {
                            var variantSelector = $('#body_font_variant');
                            variantSelector.html('');
                            $.each(data.variants,function (index,value) {
                                var nameval = value.replace('0,','');
                                nameval = nameval.replace('1,','i');
                                variantSelector.append('<option value="'+value+'">'+nameval+'</option>');
                            });
                            variantSelector.niceSelect('update');
                        }
                    });
                });

                $(document).on('change','#heading_font_family',function (e) {
                    e.preventDefault();
                    var fontFamily =  $(this).val();

                    $.ajax({
                        url: "{{route('admin.general.typography.single')}}",
                        type: "POST",
                        data:{
                            _token: "{{csrf_token()}}",
                            font_family : fontFamily
                        },
                        success:function (data) {
                            var variantSelector = $('#heading_font_variant');
                            variantSelector.html('');
                            $.each(data.variants,function (index,value) {
                                var nameval = value.replace('0,','');
                                nameval = nameval.replace('1,','i');
                                variantSelector.append('<option value="'+value+'">'+nameval+'</option>');
                            });

                            variantSelector.niceSelect('update');
                        }
                    });

                });

                // google font use
                $(document).on('change','#google_font_family',function (e) {
                    e.preventDefault();
                    var fontFamily =  $(this).val();

                    $.ajax({
                        url: "{{route('admin.general.typography.single')}}",
                        type: "POST",
                        data:{
                            _token: "{{csrf_token()}}",
                            font_family : fontFamily
                        },
                        success:function (data) {
                            var variantSelector = $('#heading_font_variant');
                            variantSelector.html('');
                            $.each(data.variants,function (index,value) {
                                var nameval = value.replace('0,','');
                                nameval = nameval.replace('1,','i');
                                variantSelector.append('<option value="'+value+'">'+nameval+'</option>');
                            });

                            variantSelector.niceSelect('update');
                        }
                    });

                });

                if($('.select2_activation').length > 0){
                    $('.select2_activation');
                }
                var dependendFields = $('select[name="heading_font_family"],#heading_font_variant');
                if(!$('input[name="heading_font"]').prop('checked')){
                    dependendFields.parent().hide()
                }

                // google heading font on off button
                $(document).on('change','input[name="heading_font"]',function (e) {
                    if(!$(this).prop('checked')){
                        dependendFields.parent().hide();
                    }else{
                        dependendFields.parent().show();
                    }
                });


                // custom font start
                if ($("#custom_font").is(':checked')){
                    $('.google_font_hide_and_show').hide();
                    $('.google_font_title_button').hide();
                }else{
                    $('.custom_font_hide_and_show').hide();
                    $('.custom_font_upload').hide();
                }

                $("#custom_font").on('change', function() {
                    if ($("#custom_font").is(':checked')){
                        $('.google_font_hide_and_show').hide();
                        $('.custom_font_hide_and_show').show();
                        $('.custom_font_title_button').show();
                        $('.custom_font_upload').show();
                    }else {
                        $('.google_font_hide_and_show').show();
                        $('.google_font_title_button').show();
                        $('.custom_font_hide_and_show').hide();
                        $('.custom_font_title_button').show();
                        $('.custom_font_upload').hide();
                    }
                });
                // custom font end

                $(document).on('click', '#typography_submit_btn', function (e) {
                    e.preventDefault();
                    var $form = $(this).closest('form');
                    $(this).text('Updating...').prop('disabled', true);
                    $form.trigger('submit');
                });



                // custom heading font add
                $(document).on('click','.custom_heading_swal_status_change',function(e){
                    e.preventDefault();
                    Swal.fire({
                        title: '{{__("Are you sure to make as this default heading font? ")}}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).next().find('.custom_heading_font_form_submit_btn').trigger('click');
                        }
                    });
                });

                // custom heading font add
                $(document).on('click','.custom_body_swal_status_change',function(e){
                    e.preventDefault();
                    Swal.fire({
                        title: '{{__("Are you sure to make as this default body font?")}}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).next().find('.custom_body_font_form_submit_btn').trigger('click');
                        }
                    });
                });

                $(document).on('click','.swal_delete_button',function(e){
                    e.preventDefault();
                    Swal.fire({
                        title: '{{__("Are you sure?")}}',
                        text: '{{__("You would not be able to revert this item!")}}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).next().find('.swal_form_submit_btn').trigger('click');
                        }
                    });
                });
            });
        }(jQuery));
    </script>
@endsection
