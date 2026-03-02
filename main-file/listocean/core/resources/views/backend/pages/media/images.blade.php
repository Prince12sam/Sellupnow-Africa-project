@extends('backend.admin-master')
@section('site-title')
    {{__('Media Images Settings')}}
@endsection
@section('style')
    <x-media.css/>
    <style>
        .media-image-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .media-image-header h2 {
            font-size: 26px;
            line-height: 30px;
        }
        .media-uploader-image-list.media-page{
            width: 100%;
            max-height: 100%;
        }
        .attachment-preview {
            position: relative;
            box-shadow: inset 0 0 15px rgb(0 0 0 / 10%), inset 0 0 0 1px rgb(0 0 0 / 5%);
            background: #eee;
            cursor: pointer;
            width: 100px;
            height: 100px;
        }
        .media-uploader-image-info {
            padding: 20px;
            display: inline-block;
            width: 100%;
        }
        .img-alt-wrap input {
            width: calc(100% - 60px);
        }
        .display-none{
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Media Images') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="btn-wrapper">
                                <a href="#" class="cmnBtn btn_5 btn_bg_blue radius-5" data-bs-toggle="modal" data-bs-target="#media_image_upload_modal">{{__('Add New Image')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <div class="row g-3 mt-1">
                    <div class="col-xxl-9 col-lg-9 col-sm-6">
                        <div class="dashboard__rates__card desktop-center radius-10">
                            <div class="dashboard__rates__card__thumb">
                                <ul class="media-uploader-image-list media-page">
                                    @foreach($all_media_images as $image)
                                        <li data-date="{{$image->updated_at}}"
                                            data-imgid="{{$image->id}}"
                                            data-imgsrc="{{asset('assets/uploads/media-uploader/'.$image->path)}}"
                                            data-size="{{$image->size}}"
                                            data-dimension="{{$image->dimensions}}"
                                            data-title="{{$image->title}}"
                                            data-alt="{{$image->alt}}">
                                            <div class="attachment-preview">
                                                <div class="thumbnail">
                                                    <div class="centered">
                                                        {!! render_image_markup_by_attachment_id($image->id,'','thumb') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-3 col-sm-6">
                         <div class="dashboard__rates__card desktop-center radius-10">
                            <div class="img-sticky-wrap" >
                                <div class="media-uploader-image-info" id="media-uploader-image-info">
                                    <div class="img-wrapper">
                                        <img src="" alt="">
                                    </div>
                                    <div class="img-info">
                                        <h5 class="img-title"></h5>
                                        <ul class="img-meta display-none">
                                            <li class="date"></li>
                                            <li class="dimension"></li>
                                            <li class="size"></li>
                                            <li class="image_id display-none"></li>
                                            <li class="imgsrc"></li>
                                            <li class="imgalt">
                                                <div class="img-alt-wrap">
                                                    <input type="text" name="img_alt_tag">
                                                    <button class="btn btn-success img_alt_submit_btn"><i class="fas fa-check"></i></button>
                                                </div>
                                            </li>
                                        </ul>
                                        <form method="post" action="{{route('admin.upload.media.file.delete')}}" class="delete_image_form display-none">
                                            @csrf
                                            <input type="hidden" name="img_id" id="info_image_id_input">
                                            <button type="submit" class=" btn btn-lg btn-danger btn-sm mb-3 mr-1"><i class="ti-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- media upload Modal -->
    <div class="modal fade" id="media_image_upload_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal_xl__fixed">
            <div class="popup_contents modal-content">
                <div class="popup_contents__header">
                    <div class="popup_contents__header__flex">
                        <div class="popup_contents__header__contents">
                            <h2 class="popup_contents__header__title">{{ __('Upload Images') }}</h2>
                        </div>
                        <div class="popup_contents__header__close" data-bs-dismiss="modal">
                            <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                        </div>
                    </div>
                </div>
                <div class="popup_contents__body">
                    <div class="alert alert-warning">{{__('Reload the page to see latest uploaded images')}}</div>
                    <div class="dragDrop_item dragDropWrapper mt-4">
                        <div class="dragDrop__area radius-10">
                            <div class="dragDrop__area__uploads dragDroparea">
                                <form action="{{route('admin.upload.media.file')}}" method="post" class="dropzone" enctype="multipart/form-data">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                        <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
    <script>
        (function($){
            "use strict";

            $(document).ready(function (){
                $(window).on('scroll',function (e){
                    var scrolltop = $(window).scrollTop();
                    var mtop = scrolltop - 400;
                    if (scrolltop > 450){
                        $('#media-uploader-image-info').css({marginTop: mtop+'px'});
                    }else{
                        $('#media-uploader-image-info').css({marginTop: '0px'});
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
