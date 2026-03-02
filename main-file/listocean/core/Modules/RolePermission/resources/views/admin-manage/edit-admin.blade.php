@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Admin')}}
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
                            <h4 class="dashboard__inner__header__title">{{ __('Edit New Admin') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <a href="{{ route('admin.all') }}" class="cmnBtn btn_5 btn_bg_info radius-5">{{__('All Admins')}}</a>
                        </div>
                    </div>
                </div>
                <div class="dashboard__inner__item">
                    <x-validation.error />
                    <form action="{{ route('admin.edit',$admin->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <x-form.text :title="__('Name')" :type="'text'" :name="'name'" :value="$admin->name" :class="'form-control'" :placeholder="__('Enter name')" />
                        <x-form.text :title="__('Username')" :type="'text'" :name="'username'" :value="$admin->username" :class="'form-control'" :placeholder="__('Enter username')" />
                        <x-form.text :title="__('Email')" :type="'email'" :name="'email'" :value="$admin->email" :class="'form-control'" :placeholder="__('Enter email')" />
                        <x-form.text :title="__('Phone')" :type="'text'" :name="'phone'" :value="$admin->phone" :class="'form-control'" :placeholder="__('Enter phone')" />
                        <x-backend.image :title="__('Profile Image')" :name="'image'" :dimentions="'48x48'" :id="$admin->image"/>

                        <div class="single-input mt-3">
                            <label class="label-title">{{ __('Select Role') }}</label>
                            <select name="role" class="form__control select2_activation">
                                <option disabled>{{ __('Select Role') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{$role}}" @if(in_array($role,$admin_role)) selected @endif>{{$role}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form__input__single mt-2">
                            <label for="about" class="form__input__single__label">{{ __('About') }}</label>
                            <textarea id="about" name="about" class="form__control radius-5" cols="100" rows="3">{{ $admin->about }}</textarea>
                        </div>

                        <div class="popup_contents__footer justify-content-start">
                            <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
@endsection
