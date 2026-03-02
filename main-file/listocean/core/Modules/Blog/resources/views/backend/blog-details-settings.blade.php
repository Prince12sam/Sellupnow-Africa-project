@extends('backend.admin-master')
@section('site-title')
    {{__('Blog Details Settings')}}
@endsection
@section('content')
<div class="row g-4 mt-0">
    <div class="col-xl-12 col-lg-12 mt-0">
        <div class="dashboard__card bg__white padding-20 radius-10">
            <h2 class="dashboard__card__header__title mb-3">{{__('Blog Details Settings')}}</h2>
            <x-validation.error/>
            <form action="{{route('admin.blog.details.settings.update')}}" method="POST" class="validateForm" enctype="multipart/form-data">
                @csrf
                <div class="form__input__flex">
                    <div class="form__input__single">
                        <label for="blog_share_title" class="form__input__single__label">{{__('Blog Share Title')}}</label>
                        <input type="text" name="blog_share_title"  class="form__control radius-5" value="{{get_static_option('blog_share_title')}}" id="blog_share_title">
                    </div>
                    <div class="form__input__single">
                        <label for="blog_tag_title" class="form__input__single__label">{{__('Blog Tags Title')}}</label>
                        <input type="text" name="blog_tag_title"  class="form__control radius-5" value="{{get_static_option('blog_tag_title')}}" id="blog_tag_title">
                    </div>
                    <div class="form__input__single">
                        <label for="related_blog_title" class="form__input__single__label">{{__('Related Blog Title')}}</label>
                        <input type="text" name="related_blog_title"  class="form__control radius-5" value="{{get_static_option('related_blog_title')}}" id="related_blog_title">
                    </div>
                    <div class="form__input__single">
                        <label for="blog_comment_load_more_title" class="form__input__single__label">{{__('Blog Comment Title')}}</label>
                        <input type="text" name="blog_comment_load_more_title"  class="form__control radius-5" value="{{get_static_option('blog_comment_load_more_title')}}" id="blog_comment_load_more_title">
                    </div>
                    <div class="form__input__single">
                        <label for="blog_comment_message_title" class="form__input__single__label">{{__('Blog Comment Message Title')}}</label>
                        <input type="text" name="blog_comment_message_title"  class="form__control radius-5" value="{{get_static_option('blog_comment_message_title')}}" id="blog_comment_message_title">
                    </div>
                    <div class="form__input__single">
                        <label for="blog_comment_button_title" class="form__input__single__label">{{__('Blog Comment Button Title')}}</label>
                        <input type="text" name="blog_comment_button_title"  class="form__control radius-5" value="{{get_static_option('blog_comment_button_title')}}" id="blog_comment_button_title">
                    </div>
                </div>
                <div class="btn_wrapper mt-4">
                    <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-icon.icon-picker/>
                <x-btn.update/>
            });
        }(jQuery));
    </script>
@endsection
