@extends('backend.admin-master')
@section('site-title')
    {{__('All Admins')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Add New Admin') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <a href="{{ route('admin.all') }}" class="cmnBtn btn_5 btn_bg_info radius-5">{{__('All Admins')}}</a>
                        </div>
                    </div>
                </div>
                <div class="dashboard__inner__item">
                    <x-validation.error />
                    <form action="{{ route('admin.create') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <x-form.text :title="__('Name')" :type="'text'" :name="'name'" :class="'form-control'" :placeholder="__('Enter name')" />
                        <x-form.text :title="__('Username')" :type="'text'" :name="'username'" :class="'form-control'" :placeholder="__('Enter username')" />
                        <x-form.text :title="__('Email')" :type="'email'" :name="'email'" :class="'form-control'" :placeholder="__('Enter email')" />
                        <x-form.text :title="__('Phone')" :type="'text'" :name="'phone'" :class="'form-control'" :placeholder="__('Enter phone')" />
                        <x-form.text :title="__('Password')" :type="'password'" :name="'password'" :class="'form-control'" :placeholder="__('Enter password')" />
                        <x-form.text :title="__('Confirm Password')" :type="'password'" :name="'password_confirmation'" :class="'form-control'" :placeholder="__('Confirm password')" />
                        <x-backend.image :title="__('Profile Image')" :name="'image'" :dimentions="'48x48'"/>

                        <div class="single-input mt-3">
                            <label class="label-title">{{ __('Select Role') }}</label>
                            <select name="role" class="form__control select2_activation">
                                <option disabled>{{ __('Select Role') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{$role}}">{{$role}}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-form.textarea :title="__('About')" :type="'text'" :name="'about'" :class="'form-control'" :placeholder="__('About')" />

                        <div class="popup_contents__footer justify-content-start">
                            <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
     </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
@endsection
