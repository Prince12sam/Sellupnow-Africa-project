@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Words Settings')}}
@endsection
@section('style')
    <x-datatable.css/>
    <style>
        /* language translate */
        .language-word-translate-box .middle-part {
            height: 300px;
            background-color: #e2e2e2;
            padding: 20px;
            overflow-y: auto;
        }

        .language-word-translate-box .top-part .single-string-wrap,
        .language-word-translate-box .middle-part .single-string-wrap {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 5px 20px;
            cursor: pointer;
        }

        .language-word-translate-box .top-part .single-string-wrap div,
        .language-word-translate-box .middle-part .single-string-wrap div {
            width: 50%;
            display: inline-block;
        }

        .language-word-translate-box .middle-part .single-string-wrap:nth-child(odd) {
            background-color: #d6d6d6;
        }

        .language-word-translate-box .top-part {
            background-color: #333;
            color: #fff;
        }

        .language-word-translate-box .top-part .single-string-wrap {
            font-size: 16px;
            font-weight: 700;
        }

        .language-word-translate-box .footer-part {
            margin-top: 30px;
        }

        .language-word-translate-box .footer-part h6 {
            margin: 10px 0 20px;
            font-size: 14px;
        }

        .language-word-translate-box .search-box-wrapper input {
            display: block;
            width: 100%;
            padding: 10px;
            border: 1px solid #333;
            margin-bottom: 20px;
        }

        @media screen and (min-width:992px) and (max-width: 1199px) {
            .page-container .main-content .card-body {
                padding: 10px;
            }
        }

        @media screen and (min-width:300px) and (max-width: 991px) {
            .page-container .main-content .card-body {
                padding: 10px;
            }
        }

        @media screen and (min-width:300px) and (max-width: 375px) {
            .page-container .main-content .card-body {
                padding: 5px;
            }
        }

        div#DataTables_Table_0_wrapper {
            overflow-x: auto;
        }

        table.dataTable thead th,
        table.dataTable thead td {
            padding: 10px 18px;
            border-bottom: 1px solid #111;
            min-width: 50px;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            flex-wrap: wrap;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            margin-top: 10px;
        }

        .table-wrap.table-responsive .table thead th {
            min-width: 50px;
        }

        @media only screen and (max-width: 767px) {
            .table-wrap.table-responsive .table thead th {
                min-width: 120px;
            }
        }
        .dataTables_wrapper {
            overflow-x: auto;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .page-container .main-content .card-body {
            overflow-x: auto;
        }

        @media screen and (max-width: 400px) {
            .all-widgets.available-form-field {
                display: grid;
            }
            .all-widgets.available-form-field li {
                width: calc(100% / 1 - 0px);
            }
        }

        .max-width-100{
            max-width: 100px;
        }

        .single-orders {
            background-color: #f78351;
        }

        .popup_contents {
            min-width: 700px;
        }
        .popup_contents__body {
            padding: 0px 27px 12px;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Change All Words')}}</h2>
                <x-validation.error/>
                 <div class="top-part d-flex justify-content-between margin-bottom-40">
                     <div class="btn_wrapper mt-4">
                       <button class="cmnBtn btn_5 btn_bg_blue radius-5"  data-bs-toggle="modal"  data-bs-target="#view_quote_details_modal">{{__('Add New Word')}}</button>
                       <button type="button" id="regenerate_source_text_btn" class="cmnBtn btn_5 btn_bg_secondary radius-5">{{ __('Regenerate Source Texts') }}</button>
                     </div>
                     <div class="left-item">
                         <div class="btn_wrapper mt-4">
                             <a class="cmnBtn btn_5 btn_bg_info radius-5" href="{{route('admin.languages')}}"> {{ __('All Languages') }} </a>
                         </div>
                     </div>
                 </div>
                <div class="alert alert_margin alert_info">
                    <i class="las la-info-circle"></i>
                    <strong>{{ __('select any source text to translate it, then enter your translated text in textarea hit update') }}</strong>
                </div>
                <div class="language-word-translate-box">
                    <div class="search-box-wrapper">
                        <input type="text" name="word_search" id="word_search" placeholder="{{__('Search Source Text...')}}">
                    </div>
                    <div class="top-part">
                        <div class="single-string-wrap">
                            <div class="string-part">{{__('Source Text')}}</div>
                            <div class="translated-part">{{__('Translation')}}</div>
                        </div>
                    </div>
                    <div class="middle-part">
                        @foreach($all_word as $key => $value)
                            <div class="single-string-wrap">
                                <div class="string-part text-dark" data-key="{{$key}}">{{$key}}</div>
                                <div class="translated-part" data-trans="{{$value}}">{{$key === $value ? '' : $value}}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="footer-part">
                        <h6 id="selected_source_text"><span>{{__('Source Text:')}}</span> <strong class="text"></strong></h6>
                        <form action="{{route('admin.languages.words.update',$lang_slug)}}" method="POST" id="langauge_translate_form">
                            @csrf
                            <input type="hidden" name="type" value="{{$type}}">
                            <input type="hidden" name="string_key">
                            <div class="from-group">
                                <label for="translate_word" class="form__input__single__label">{{__('Translate To')}} <strong>{{$language->name}}</strong></label>
                                <textarea name="translate_word" cols="10" rows="5" class="form__control radius-5" placeholder="{{__('enter your translate words')}}"></textarea>
                            </div>
                            <div class="btn_wrapper mt-4">
                                <button type="submit" id="language_update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Languages Edit Modal -->
    <div class="modal fade" id="view_quote_details_modal">
        <div class="modal-dialog">
            <div class="popup_contents modal-content">
                <div class="popup_contents__header">
                    <div class="popup_contents__header__flex">
                        <div class="popup_contents__header__contents">
                            <h2 class="popup_contents__header__title">{{ __('Add New Translate able String') }}</h2>
                        </div>
                        <div class="popup_contents__header__close" data-bs-dismiss="modal">
                            <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                        </div>
                    </div>
                </div>
                <form action="{{route('admin.languages.add.new.word')}}" method="post">
                    @csrf
                    <div class="popup_contents__body">
                        <input type="hidden" name="lang_slug" id="lang_slug" value="{{$lang_slug}}">
                        <div class="form__input__single">
                            <label for="new_string" class="form__input__single__label">{{__('String')}}</label>
                            <input type="text" class="form__control radius-5" name="new_string" placeholder="{{__('new string')}}">
                        </div>
                        <div class="form__input__single">
                            <label for="translated_string" class="form__input__single__label">{{__('Translated String')}}</label>
                            <input type="text" class="form__control radius-5" id="translated_string" name="translate_string" placeholder="{{__('Translated String')}}">
                        </div>
                    </div>
                    <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                        <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Add New String') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-datatable.js/>
    <script>
        (function($){
            "use strict";

            $(document).ready(function (){
                $(document).on('click','#language_update',function (e){
                    e.preventDefault();

                    let url =  "{{route('admin.languages.words.update',$lang_slug)}}";
                    let translateWord = $('textarea[name="translate_word"]').val();
                    let stringKey = $('input[name="string_key"]').val();
                    $.ajax({
                        url: url,
                        type: "post",
                        data: {
                            _token : "{{csrf_token()}}",
                            translate_word: translateWord,
                            string_key: stringKey,
                        },
                        success: function(data){
                            Swal.fire({
                                title: '{{__("Success")}}',
                                text: '{{__("translate success")}}',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                    });
                });

                $(document).on('click','.language-word-translate-box .middle-part .single-string-wrap .string-part',function (e){
                    e.preventDefault();
                    let langKey = $(this).data('key');
                    let langValue = $(this).next().data('trans');
                    let formContainer = $('#langauge_translate_form');
                    $('#selected_source_text strong').text(langKey);
                    formContainer.find('input[name="string_key"]').val(langKey);
                    formContainer.find('textarea[name="translate_word"]').val(langValue);
                });
                //search source text
                $(document).on('keyup','#word_search',function (e){
                    e.preventDefault();
                    let searchText = $(this).val();
                    var allSourceText = $('.language-word-translate-box .middle-part .single-string-wrap .string-part');
                    $.each(allSourceText,function (index,value){
                        var text = $(this).text();
                        var found = text.toLowerCase().match(searchText.toLowerCase().trim());
                        if (!found){
                            $(this).parent().hide();
                        }else{
                            $(this).parent().show();
                        }
                    });
                });

                $(document).on('click','#regenerate_source_text_btn',function (e){
                    e.preventDefault();
                    Swal.fire({
                        title: '{{__("Are you sure?")}}',
                        text: '{{__("It will delete current source texts, you will lose your current translated data!")}}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{__('Yes, Generate!')}}",
                        cancelButtonText: "{{__('Yes, Cancel!')}}",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                url: "{{route('admin.languages.regenerate.source.texts')}}",
                                data: {
                                    _token : "{{csrf_token()}}",
                                    slug : "{{$language->slug}}"
                                },
                                success : function (){
                                    toastr.success("{{__('source text generate success')}}")
                                    location.reload();
                                }
                            });
                        }
                    });
                });
            });
        })(jQuery);
    </script>
@endsection
