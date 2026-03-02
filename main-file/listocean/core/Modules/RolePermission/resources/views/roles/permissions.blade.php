@extends('backend.admin-master')
@section('site-title')
    {{ __('Assign Permission') }}
@endsection
@section("style")
    <style>
        :root {
            --paragraph-color-one: #73777D;
            --bs-dropdown-item-padding-y: 0.25rem;
            --bs-dropdown-item-padding-x: 1rem;
            --bs-dropdown-header-color: #6c757d;
            --heading-font: "Poppins", sans-serif;
            --main-color-one: #696CFF;
            --white: #fff;
        }

        .simplePresentCart-one {
            padding: 30px 24px;
            border-radius: 16px;
        }

        .white-bg {
            background: white;
        }

        .mb-24 {
            margin-bottom: 24px;
        }

        .mb-30 {
            margin-bottom: 30px;
        }

        .section-tittle-one .title {
            color: #151D26;
            font-family: "Poppins", sans-serif;
            text-transform: capitalize;
            font-size: 18px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 10px;
            display: inline-block;
            position: relative;
            z-index: 0;
        }

        .cmn-btn.style-3 {
            overflow: hidden;
            -webkit-transition: border-color 0.3s, background-color 0.3s;
            transition: border-color 0.3s, background-color 0.3s;
            -webkit-transition-timing-function: cubic-bezier(0.2, 1, 0.3, 1);
            transition-timing-function: cubic-bezier(0.2, 1, 0.3, 1);
        }

        .cmn-btn.style-7::after, .cmn-btn.style-5 span, .cmn-btn.style-5::before, .cmn-btn.style-5, .cmn-btn.style-3::after, .cmn-btn.style-3, .cmn-btn {
            padding: 7px 16px;
        }

        .cmn-btn {
            display: inline-block;
            min-width: 100px;
            margin-bottom: 10px;
            border: inherit;
            background: inherit;
            vertical-align: middle;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .border-style-solid {
            border-style: solid !important;
        }

        .border-1 {
            border-width: 1px !important;
        }

        .border-main-one {
            border-color: #696CFF !important;
            color: #696CFF;
        }

        .radius-16 {
            border-radius: 16px !important;
        }

        .btn-danger {
            background-color: #d22d3d !important;
            border-color: #d22d3d !important;
        }

        .custom-dataTable, .custom-dataTable * {
            font-size: 12px;
        }

        .custom-dropdown button {
            background: none;
            padding: 0;
            border: 0;
            font-size: 40px;
            color: #A1A5A8;
            line-height: 1;
        }

        .custom-dataTable, .custom-dataTable * {
            font-size: 12px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .custom-dataTable, .custom-dataTable * {
            font-size: 12px;
        }
        .dropdown-toggle {
            white-space: nowrap;
        }

        .custom-dataTable .custom-dropdown  button > i {
            font-size: 30px !important;
            font-weight: 700;
            color: #333;
            border: 1px solid #e2e2e2e2;
            border-radius: 10px;
            line-height: 20px;
            padding: 2px 6px;
        }

        .dropdown-menu {
            border: 0;
            -webkit-box-shadow: 0 3px 12px rgba(45, 23, 191, 0.09);
            box-shadow: 0 3px 12px rgba(45, 23, 191, 0.09);
        }
        .custom-dataTable, .custom-dataTable * {
            font-size: 12px;
        }

        .swal_delete_button {
            cursor: pointer;
        }

        .dropdown-item {
            font-weight: 500;
            color: var(--paragraph-color-one);
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: var(--bs-dropdown-item-padding-y) var(--bs-dropdown-item-padding-x);
            clear: both;
            font-weight: 400;
            color: var(--bs-dropdown-link-color);
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }

        .dropdown-toggle::after{
            display: none;
        }


        .custom-dataTable, .custom-dataTable * {
            font-size: 12px;
        }

        .custom-dataTable td {
            font-size: 12px;
        }

        .cmn-btn1 {
            font-family: var(--heading-font);
            -webkit-transition: 0.4s;
            transition: 0.4s;
            border: 1px solid transparent;
            background: var(--main-color-one);
            color: var(--white);
            padding: 13px 15px;
            font-size: 16px;
            font-weight: 500;
            display: inline-block;
            border-radius: 30px;
            text-align: center;
            text-transform: capitalize;
        }
        .font-weight-bold {
            font-weight: bold;
        }

        .checkboxed {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: start;
            -ms-flex-align: start;
            align-items: flex-start;
            cursor: pointer;
            gap: 10px;
        }

        .checkboxed.style-02 .checkboxed__input:checked+.checkboxed__label {
            color: var(--main-color-one);
        }

        .checkboxed__input {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            height: 18px;
            width: 18px;
            cursor: pointer;
            background: #fff;
            border: 1px solid #dddddd;
            border-radius: 0px;
            margin-top: 4px;
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
        }

        .checkboxed__input::after {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 10px;
            color: #fff;
            visibility: hidden;
            opacity: 0;
            -webkit-transform: scale(1.6) rotate(90deg);
            transform: scale(1.6) rotate(90deg);
            -webkit-transition: all 0.2s;
            transition: all 0.2s;
        }

        .checkboxed__input:checked {
            background: var(--main-color-one);
            border-color: var(--main-color-one);
            background: var(--main-color-one);
        }

        .checkboxed__input:checked::after {
            visibility: visible;
            opacity: 1;
            -webkit-transform: scale(1) rotate(0deg);
            transform: scale(1) rotate(0deg);
        }

        .checkboxed__label {
            cursor: pointer;
            text-align: left;
            line-height: 26px;
            font-size: 16px;
            font-weight: 400;
            color: var(--heading-color);
            margin: 0;
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
        }

        .radioboxed {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: start;
            -ms-flex-align: start;
            align-items: flex-start;
            cursor: pointer;
            gap: 10px;
        }

        .radioboxed.style-02 .radioboxed__input:checked+.radioboxed__label {
            color: var(--main-color-one);
        }

        .radioboxed__input {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            height: 22px;
            width: 22px;
            border-radius: 50%;
            cursor: pointer;
            background: #fff;
            border: 1px solid #dddddd;
            margin-top: 4px;
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
            position: relative;
        }

        .radioboxed__input::after {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            height: 12px;
            width: 12px;
            border-radius: 50%;
            background-color: var(--main-color-one);
            visibility: hidden;
            opacity: 0;
            -webkit-transform: scale(0.2) rotate(90deg);
            transform: scale(0.2) rotate(90deg);
            -webkit-transition: all 0.2s;
            transition: all 0.2s;
        }

        .radioboxed__input:checked {
            border-color: var(--main-color-one);
        }

        .radioboxed__input:checked::after {
            visibility: visible;
            opacity: 1;
            -webkit-transform: scale(1) rotate(0deg);
            transform: scale(1) rotate(0deg);
        }

        .radioboxed__label {
            cursor: pointer;
            text-align: left;
            line-height: 26px;
            font-size: 16px;
            font-weight: 400;
            color: var(--heading-color);
            margin: 0;
            -webkit-box-flex: 1;
            -ms-flex: 1;
            flex: 1;
        }

        .custom-switch {
            height: 0;
            width: 0;
            visibility: hidden;
            position: absolute;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .custom-switch:checked+.switch-label {
            background: var(--main-color-one);
        }

        .custom-switch:checked+.switch-label::after {
            left: calc(100% - 5px);
            -webkit-transform: translateX(-100%);
            transform: translateX(-100%);
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
        }

        .switch-label {
            cursor: pointer;
            text-indent: -9999px;
            width: 50px;
            height: 25px;
            background: #DDDDDD;
            display: block;
            border-radius: 100px;
            position: relative;
        }

        .switch-label::after {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            width: 25px;
            height: 15px;
            background: #fff;
            border-radius: 60px;
            -webkit-transition: 0.3s;
            transition: 0.3s;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{$role->name}}: {{ __("Permissions") }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <a class="cmnBtn btn_5 btn_bg_info radius-5" href="{{ route('admin.role.create') }}"> {{__('All Roles')}}</a>
                        </div>
                    </div>
                </div>
            <div class="permission-wrap">
                <form action="{{route("admin.role.permission.create",$role->id)}}" method="post">
                    @csrf

                    <div class="checkbox-wrapper">
                        @foreach($permissions as $key => $permission_value)
                            @php
                                $groupName = str_replace("-", " ", strtolower($key));
                            @endphp
                            <div class="permission-group-wrapper">
                                <div class="permission-group-header">
                                    <h5 class="permission-group-header-title mt-4 mb-3">
                                        {{ ucwords($groupName) }}

                                        <div class="vendor-coupon-switch mt-3">
                                            <input class="custom-switch permisssion-group-switch" type="checkbox" id="permisssion-group-switch-{{ $groupName }}" />
                                            <label class="switch-label permisssion-group-switch" for="permisssion-group-switch-{{ $groupName }}"></label>
                                        </div>
                                    </h5>
                                </div>
                                <div class="row g-4">
                                    @foreach($permission_value as $permission)
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="form-group d-flex justify-content-start gap-3 mt-2 p-0">
                                                <div class="vendor-coupon-switch mt-2">
                                                    <input @if(in_array($permission->id,$rolePermissions)) checked @endif
                                                    class="permission-switch custom-switch permisssion-switch-{{$permission->id}}"
                                                           type="checkbox"
                                                           id="permisssion-switch-{{$permission->id}}"
                                                           name="permission[]"
                                                           value="{{$permission->id}}"/>
                                                    <label class="switch-label permisssion-switch-{{$permission->id}}" for="permisssion-switch-{{$permission->id}}"></label>
                                                </div>
                                                <label class="m-0" for="permisssion-switch-{{$permission->id}}">
                                                    <strong>{{ ucfirst(str_replace(['-','.'],[' ',' '],$permission->name)) }}</strong>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="btn-wrapper mt-4">
                        <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __("Submit Now") }}</button>
                    </div>
                </form>
            </div>
          </div>
        </div>
    </div>
@endsection
@section("scripts")
    <script>
        // handle group switch button click
        $(document).on("change",".permisssion-group-switch", function (){
            // get current element
            let currentEl = $(this);
            // select permission group wrapper
            let permissionGroup = currentEl.closest(".permission-group-wrapper");
            // get all the buttons that are available in this group
            let availableSwitch = permissionGroup.find('.permission-switch');

            // now check currentEl is checked or not if checked then select all available switches if not then de-checked all switch
            if(currentEl.is(':checked')){
                // run a loop here for checked all available options
                availableSwitch.each(function (){
                    $(this).prop("checked", true);
                });
            }else{
                availableSwitch.each(function (){
                    $(this).prop("checked", false);
                });
            }
        });

        $(document).on("click", ".permission-switch", function (){
            // get this input group first
            let currentEl = $(this);
            let permissionGroup = currentEl.closest(".permission-group-wrapper");

            handleGroupSwitch(permissionGroup);
        })

        // create a function for preselecting all group switches
        function handleGroupSwitch(permissionGroup = null){
            // select permission group wrapper
            let permissionGroupWrapper = (permissionGroup == null) ? $('.permission-group-wrapper') : permissionGroup;

            permissionGroupWrapper.each(function (){
                // select all available switches on this group
                let availableSwitch = $(this).find('.permission-switch').length;
                // select all checked switches
                let checkedSwitch = $(this).find('.permission-switch:checked').length;

                if(availableSwitch === checkedSwitch){
                    $(this).find('.permisssion-group-switch').prop("checked", true);
                }else{
                    $(this).find('.permisssion-group-switch').prop("checked", false);
                }
            });
        }
        handleGroupSwitch();

        // write javascript for repeater of permissions
        $(document).on("click", ".add", function (){
            $(this).closest('tr').after($(this).closest('tr').clone());
        });

        (function($){
            "use strict";
            $(document).on("click",".edit_role",function (e){
                e.preventDefault();
                let modalContainer= $("#editRoles");
                modalContainer.find("form").attr("action",$(this).data("action"));
                modalContainer.find("input[name='id']").val($(this).data("id"));
                modalContainer.find("input[name='name']").val($(this).data("name"));
            })
        })(jQuery);
    </script>
@endsection
