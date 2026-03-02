@extends('backend.admin-master')
@section('site-title')
    {{__('Third Party Scripts Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__("Third Party Scripts Settings")}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.general.scripts.settings')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__single">
                        <label for="site_third_party_tracking_code" class="form__input__single__label">{{__('Third Party Api Code')}}</label>
                        <textarea name="site_third_party_tracking_code" id="site_third_party_tracking_code" cols="30" rows="10" class="form__control radius-5">{{get_static_option('site_third_party_tracking_code')}}</textarea>
                        <p>{{__('this code will be load before </head> tag')}}</p>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
         </div>
    </div>
@endsection
