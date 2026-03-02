@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Notice')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h2 class="dashboard__card__header__title mb-3">{{__('Edit Notice')}}</h2>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="btn-wrapper">
                                <a href="{{ route('admin.all.notice') }}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('All Notices') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.notice.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="notice_id" value="{{ $notice->id }}">
                    <div class="form__input__single">
                        <label for="title" class="form__input__single__label">{{__('Title ')}}</label>
                        <input type="text" class="form__control" name="title" value="{{ $notice->title }} placeholder="{{__('title')}}">
                    </div>
                    <div class="form__input__single">
                        <label for="description" class="form__input__single__label">{{__('Notice Description')}}</label>
                        <textarea class="form__control" name="description"  cols="10" rows="3">{{ $notice->description }}</textarea>
                    </div>

                    <div class="form__input__single">
                        <label for="notice_type" class="notice-type-label"><strong>{{__('Select Notice Type')}}</strong> <span class="text-danger">*</span> </label>
                        @php
                            $noticeTypes = [ 'Error' => '1','Warning' => '2','Success' => '3',  'Info' => '4', ];
                        @endphp
                        <select name="notice_type" id="notice_type" class="form-control">
                            <option value="">{{ __('Select Notice Type') }}</option>
                            @foreach ($noticeTypes as $label => $value)
                                <option value="{{ $value }}" @if($value == $notice->notice_type) selected @endif>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        <div class="notice-descriptions" style="display: grid">
                            <small class="text-danger"> <span class="notice-error">{{__('Error')}}</span>- {{__('–Displays the message with a red background')}}</small>
                            <small class="text-warning"> <span class="notice-warning">{{__('Warning')}}</span>– {{__('Displays the message with a yellow/orange background')}}</small>
                            <small class="text-success"> <span class="success">{{__('Success')}}</span>– {{__('Displays the message with a green background')}}</small>
                            <small class="text-info"> <span class="">{{__('Info')}}</span>– {{__('Displays the message with a blue background')}}</small>
                        </div>
                    </div>

                    <div class="form__input__single">
                        <label for="notice_for" class="notice_for-label"><strong>{{__('Select Notice for')}}</strong> <span class="text-danger">*</span> </label>
                        @php
                            $noticeForOptions = [  'Frontend' => '1', 'User Dashboard' => '2'];
                        @endphp
                        <select name="notice_for" id="notice_for" class="form-control">
                            <option value="">{{ __('Select Notice for') }}</option>
                            @foreach ($noticeForOptions as $label => $value)
                                <option value="{{ $value }}" @if($value == $notice->notice_for) selected @endif>{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form__input__single">
                        <label for="expire_date">{{__('Expire Date')}} <span class="text-danger">*</span> </label>
                        <input type="text" name="expire_date" value="{{ $notice->expire_date }}" placeholder="{{ __('Select Date') }}" class="form-control mt-2 date" id="expire_date">
                    </div>

                    <div class="form__input__single">
                        <label for="status">{{__('Status')}}</label>
                        <select name="status" class="form-control" id="status">
                            <option value="1" @if($notice->status == 1) selected @endif>{{__("Active")}}</option>
                            <option value="0" @if($notice->status == 0) selected @endif>{{__("Inactive")}}</option>
                        </select>
                    </div>

                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        flatpickr('#expire_date', {
            enableTime: false,
            dateFormat:  "Y-m-d",
        });
    </script>
@endsection
