@extends('backend.admin-master')
@section('site-title')
    {{__('All Custom Form')}}
@endsection
@section('style')
    <x-datatable.js/>
    <style>
        .form__input__flex {
            gap: 4px;
        }
        div#DataTables_Table_0_filter {
            display: flex;
            justify-content: flex-end;
            padding-bottom: 13px;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-8 col-lg-8">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h2 class="dashboard__card__header__title mb-3">{{__('All Custom Form')}}</h2>
                            <x-bulk-action.bulk-action />
                        </div>
                    </div>
                </div>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_dataTable">
                        <table class="dataTablesExample">
                            <thead>
                            <th class="no-sort">
                                <div class="mark-all-checkbox">
                                    <input type="checkbox" class="all-checkbox">
                                </div>
                            </th>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Receiving Email') }}</th>
                            <th>{{ __('Action') }}</th>
                            </thead>
                            <tbody>
                            @foreach ($all_forms as $form)
                                <tr>
                                    <td> <x-bulk-action.bulk-delete-checkbox :id="$form->id" /> </td>
                                    <td>{{ $form->id }}</td>
                                    <td>{{ $form->title }}</td>
                                    <td>{{ $form->email }}</td>
                                    <td>
                                        <x-btn.edit :url="route('admin.form.edit', $form->id)" />
                                        <x-popup.delete-popup :url="route('admin.form.delete', $form->id)" />
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-4">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Add New Form')}}</h2>
                <x-validation.error />
                <form action="{{route('admin.form')}}" method="POST" class="new_language_form">
                    @csrf
                        <div class="form__input__flex">
                            <label for="title" class="label-title">{{ __('Title') }}</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"  placeholder="{{ __('Enter form title') }}" class="form__control radius-5">
                        </div>
                        <div class="form__input__flex mt-4">
                            <label for="email" class="label-title">{{ __('Receiving Email') }}</label>
                            <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __('Enter receiving email') }}" class="form__control radius-5">
                        </div>
                        <div class="form__input__flex mt-4">
                            <label for="button_text" class="label-title">{{ __('Button Title') }}</label>
                            <input type="text" name="button_text" id="button_text"
                                   value="{{ old('button_text') }}" placeholder="{{ __('Enter button title') }}"
                                   class="form__control radius-5">
                        </div>
                        <div class="form__input__flex mt-4 mb-3">
                            <label for="success_message" class="label-title">{{ __('Success Message') }}</label>
                            <input type="text" name="success_message" id="success_message"
                                   value="{{ old('success_message') }}"
                                   placeholder="{{ __('Enter success message') }}" class="form__control radius-5">
                        </div>
                        <div class="btn_wrapper mt-4">
                            <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Add New Form') }}</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-datatable.js/>
    <x-bulk-action.bulk-delete-js :url="route('admin.delete.bulk.action.form')" />
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('.DataTable_activation').DataTable();
            });
        }(jQuery));
    </script>
@endsection
