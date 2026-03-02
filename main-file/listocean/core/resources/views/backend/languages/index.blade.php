@extends('backend.admin-master')
@section('site-title')
    {{__('Language Settings')}}
@endsection
@section('style')
    <x-datatable.css/>
    <style>
        .select2-container {
             z-index: 0;
        }

        /* styles.css */

        .dropdown-menu {
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu li {
            list-style: none;
            margin: 10px 10px 10px 10px; /* Space between list items */
            justify-content: center!important;
            align-items: center;
            text-align: center;
        }

        .dropdown-menu li a{
         justify-content: center!important;
        }

        .dropdown-menu li a i{
         justify-content: center!important;
        }

        .dropdown-menu li:last-child {
            margin-bottom: 5px; /* Remove margin from the last item */
        }

        .dropdown-menu .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #e2e6ea;
        }

        .dropdown-menu .dropdown-item i {
            margin-left: 10px;
        }

    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-9 col-lg-9">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Language Settings')}}</h2>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_dataTable">
                        <table class="dataTablesExample">
                            <thead>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Direction')}}</th>
                                <th>{{__('Slug')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Default')}}</th>
                                <th>{{__('Action')}}</th>
                            </thead>
                            <tbody>
                                @foreach($all_lang as $data)
                                    <tr>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->name}}</td>
                                        <td>{{strtoupper($data->direction)}}</td>
                                        <td>{{$data->slug}}</td>
                                        <td>{{$data->status}}</td>
                                        <td>
                                            @can('languages-words-edit')
                                                @if($data->default == 1)
                                                    <a href="javascript:void(0)" class="alert alert-success">{{__("Default")}}</a>
                                                @elseif($data->status === 'publish')
                                                    <x-status.change-default-lang :url="route('admin.languages.default',$data->id)"/>
                                                @endif
                                            @endcan
                                        </td>
                                        <td>
                                            <!-- DropDown -->
                                            <div class="dropdown custom__dropdown">
                                                <button class="dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="las la-ellipsis-h"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end " aria-labelledby="dropdownMenuButton1">
                                                    @can('languages-words-edit')
                                                        @if($data->default != 1)
                                                            <li>
                                                               <x-popup.delete-popup :url="route('admin.languages.delete',$data->id)"/>
                                                            <li>
                                                        @endif
                                                    @endcan
                                                   <li>
                                                        <a class="dropdown-item" href="{{route('admin.languages.words.all',$data->slug)}}"
                                                           title="{{__('Edit Frontend Words')}}" class="cmnBtn btn_5 btn_bg_info radius-5">
                                                            {{__('Edit All Words')}}
                                                        </a>
                                                    <li>
                                                    @can('languages-words-edit')
                                                      <li>
                                                          <a href="#"
                                                             data-bs-toggle="modal"
                                                             data-bs-target="#language_item_edit_modal"
                                                             class="cmnBtn btn_5 btn_bg_warning radius-5 btnIcon radius-5 lang_edit_btn"
                                                             data-id="{{$data->id}}"
                                                             data-name="{{$data->name}}"
                                                             data-slug="{{$data->slug}}"
                                                             data-status="{{$data->status}}"
                                                             data-direction="{{$data->direction}}">
                                                              <i class="las la-pencil-alt"></i>
                                                          </a>
                                                      </li>
                                                      <li>
                                                          <a  href="#"
                                                              data-bs-toggle="modal"
                                                              data-bs-target="#language_item_clone_modal"
                                                              class="cmnBtn btn_6 btn_bg_secondary btnIcon radius-5 lang_clone_btn"
                                                              data-id="{{$data->id}}">
                                                              <i class="las la-copy"></i>
                                                          </a>
                                                      </li>
                                                    @endcan

                                                </ul>
                                            </div>


                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@can('languages-add')
        <div class="col-xl-3 col-lg-3">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Add New Language')}}</h2>
                <form action="{{route('admin.languages.new')}}" method="POST" class="new_language_form">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="name" class="form__input__single__label">{{__('Languages')}}</label>
                            <input type="hidden" name="name">
                            <select name="language_select" class="form__control radius-5">
                                <x-languages.languages-list/>
                            </select>
                        </div>
                        <div class="form__input__single">
                            <label for="direction" class="form__input__single__label">{{__('Direction')}}</label>
                            <select name="direction" id="direction" class="form__control radius-5">
                                <option value="ltr">{{__('LTR')}}</option>
                                <option value="rtl">{{__("RTL")}}</option>
                            </select>
                        </div>
                        <div class="form__input__single">
                            <label for="status" class="form__input__single__label">{{__('Status')}}</label>
                            <select name="status" id="status" class="form__control radius-5">
                                <option value="publish">{{__('Publish')}}</option>
                                <option value="draft">{{__("Draft")}}</option>
                            </select>
                        </div>
                        <div class="form__input__single">
                            <label for="slug" class="form__input__single__label">{{__('Slug')}}</label>
                            <input class="form__control" name="slug" readonly>
                        </div>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Add New') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

    <!-- Languages Edit Modal -->
    <div class="modal fade" id="language_item_edit_modal">
        <div class="modal-dialog">
            <div class="popup_contents modal-content">
                <div class="popup_contents__header">
                    <div class="popup_contents__header__flex">
                        <div class="popup_contents__header__contents">
                            <h2 class="popup_contents__header__title">{{ __('Edit Language') }}</h2>
                        </div>
                        <div class="popup_contents__header__close" data-bs-dismiss="modal">
                            <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                        </div>
                    </div>
                </div>
                <form action="{{route("admin.languages.update")}}" method="post" class="edit_language_form">
                    @csrf

                    <input type="hidden" name="lang_id" id="lang_id">

                    <div class="popup_contents__body">
                        <div class="form__input__single">
                            <label for="email" class="form__input__single__label">{{ __('Languages') }}</label>
                            <input type="hidden" name="name">
                            <select name="language_select" class="form__control radius-5">
                                <x-languages.languages-list/>
                            </select>
                        </div>
                        <div class="form__input__single">
                            <label for="direction" class="form__input__single__label">{{ __('Direction') }}</label>
                            <select name="direction" id="edit_direction" class="form-control">
                                <option value="ltr">{{__('LTR')}}</option>
                                <option value="rtl">{{__("RTL")}}</option>
                            </select>
                        </div>
                        <div class="form__input__single">
                            <label for="edit_status" class="form__input__single__label">{{ __('Status') }}</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="publish">{{__('Publish')}}</option>
                                <option value="draft">{{__("Draft")}}</option>
                            </select>
                        </div>
                        <div class="form__input__single">
                            <label for="edit_slug" class="form__input__single__label">{{ __('Slug') }}</label>
                            <input type="text" class="form__control radius-5" name="slug" id="edit_slug" readonly>
                        </div>
                    </div>
                    <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                        <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Languages clone Modal -->
    <div class="modal fade" id="language_item_clone_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="popup_contents modal-content">
            <div class="popup_contents__header">
                <div class="popup_contents__header__flex">
                    <div class="popup_contents__header__contents">
                        <h2 class="popup_contents__header__title">{{ __('Clone To New Languages') }}</h2>
                    </div>
                    <div class="popup_contents__header__close" data-bs-dismiss="modal">
                        <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                    </div>
                </div>
            </div>
            <div class="popup_contents__body">
                <span class="alert alert-info">{{__('it will copy all content of all static sections, header slider, key features, contact info, support info, pages, menus')}}</span>

                <form action="{{route('admin.languages.clone')}}" method="post" class="edit_language_form">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <div class="form__input__single">
                        <label for="email" class="form__input__single__label">{{ __('Languages') }}</label>
                        <input type="hidden" name="name">
                        <select name="language_select" class="form__control radius-5">
                            <x-languages.languages-list/>
                        </select>
                    </div>

                    <div class="form__input__single">
                        <label for="direction" class="form__input__single__label">{{__('Direction')}}</label>
                        <select name="direction" id="direction" class="form__control radius-5">
                            <option value="ltr">{{__('LTR')}}</option>
                            <option value="rtl">{{__("RTL")}}</option>
                        </select>
                    </div>
                    <div class="form__input__single">
                        <label for="status" class="form__input__single__label">{{__('Direction')}}</label>
                        <select name="status" class="form__control radius-5">
                            <option value="publish">{{__('Publish')}}</option>
                            <option value="draft">{{__("Draft")}}</option>
                        </select>
                    </div>
                    <div class="form__input__single">
                        <label for="slug" class="form__input__single__label">{{ __('Slug') }}</label>
                        <input type="text" class="form__control radius-5" name="slug" readonly>
                    </div>
                    <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                        <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Submit') }}</button>
                    </div>
                 </form>
            </div>
        </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-datatable.js/>
    <script>
        (function($){
            "use strict";

            $(document).ready(function () {

                $(document).on('change', 'select[name="language_select"]', function () {
                    let el = $(this);
                    let name = el.parent().find('select[name="language_select"] option[value="'+el.val()+'"]' ).text()
                    el.parent().find('input[name="name"]').val(name)
                    el.parent().parent().find('input[name="slug"]').val(el.val())
                });

                $(document).on('click', '.lang_edit_btn', function () {
                    let el = $(this);
                    let id = el.data('id');
                    let name = el.data('name');
                    let slug = el.data('slug');
                    let form = $('#language_item_edit_modal');


                    form.find('#lang_id').val(id);
                    form.find('input[name="name"]').val(name);
                    form.find('select[name="language_select"] option[value="'+slug+'"]').attr('selected',true);
                    form.find('#edit_slug').val(slug);
                    form.find('#edit_direction option[value="' + el.data('direction') + '"]').prop('selected', true);
                    form.find('#edit_status option[value="' + el.data('status') + '"]').prop('selected', true);
                });

                $(document).on('click', '.lang_clone_btn', function () {
                    let el = $(this);
                    let id = el.data('id');
                    let form = $('#language_item_clone_modal');
                    form.find('input[name="id"]').val(id);
                });
            });
        })(jQuery);
    </script>
@endsection
