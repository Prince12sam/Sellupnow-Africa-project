@extends('backend.admin-master')
@section('site-title')
    {{__('Reading')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="header-wrap d-flex justify-content-between mb-3">
                    <div class="left-content">
                        <h4 class="header-title">{{__('Reading Settings')}}</h4>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.general.reading')}}" method="POST" enctype="multipart/form-data">
                      @csrf
                       <div class="form__input__single">
                           <label for="site_logo" class="form__input__single__label">{{__('Home Page Display')}}</label>
                           <select name="home_page" class="form__control radius-5 select2_activation">
                               @foreach($all_home_pages as $page)
                                   <option value="{{$page->id}}" @if($page->id == get_static_option('home_page'))  selected @endif >{{$page->title}}</option>
                               @endforeach
                           </select>
                       </div>
                       <div class="form__input__single">
                           <label for="site_logo" class="form__input__single__label">{{__('Blog Page')}}</label>
                           <select name="blog_page" class="form__control radius-5 select2_activation">
                               @foreach($all_home_pages as $page)
                                   <option value="{{$page->id}}" @if($page->id == get_static_option('blog_page'))  selected @endif>{{$page->title}}</option>
                               @endforeach
                           </select>
                     </div>

                    @if(moduleExists('Membership'))
                      <div class="form__input__single">
                           <label for="membership_plan_page" class="form__input__single__label">{{__('Membership Plan Page')}}</label>
                           <select name="membership_plan_page" class="form__control radius-5 select2_activation">
                               @foreach($all_home_pages as $page)
                                   <option value="{{$page->id}}" @if($page->id == get_static_option('membership_plan_page'))  selected @endif>{{$page->title}}</option>
                               @endforeach
                           </select>
                      </div>
                    @endif

                    <div class="form__input__single">
                       <label for="listing_filter_page_id" class="form__input__single__label">{{__('Select the Page for home page search Listings display')}}</label>
                       <select name="listing_filter_page_id" class="form__control radius-5 select2_activation">
                           @foreach($all_home_pages as $page)
                               <option value="{{$page->id}}" @if($page->id == get_static_option('listing_filter_page_id'))  selected @endif>{{$page->title}}</option>
                           @endforeach
                       </select>
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
        $(document).ready(function () {
            <x-btn.update/>
            <x-icon.icon-picker/>
        });
    })(jQuery);
</script>
@endsection
