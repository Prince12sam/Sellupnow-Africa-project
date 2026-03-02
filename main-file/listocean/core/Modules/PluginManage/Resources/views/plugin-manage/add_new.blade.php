@extends('backend.admin-master')
@section('site-title')
    {{__('Add New Plugin')}}
@endsection
@section('style')
    <style>
        .padding-30{
            padding: 30px;
        }
        .form-group.plugin-upload-field {
            margin-top: 60px;
        }

        .form-group.plugin-upload-field label {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 35px;
        }

        .form-group.plugin-upload-field small {
            font-size: 12px;
            margin-top: 11px;
        }

    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-4 mt-4">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Add New Plugin') }}</h4>
                            <x-validation.error/>
                            <p>{{ __('upload new plugin from here. if you have a plugin already but you have uploaded that plugin file again, it will override existing plugins files') }}</p>
                        </div>
                    </div>
                </div>
                <form action="{{route("admin.plugin.manage.new")}}" method="POST" class="validateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single plugin-upload-field">
                            <label for="file" class="form__input__single__label">{{ __('Upload Plugin File') }}</label>
                            <input type="file" class="form__control radius-5" name="plugin_file" accept=".zip" placeholder="{{ __('upload zip file') }}">
                            <small class="d-block">{{__("only zip file accepted")}}</small>
                        </div>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
