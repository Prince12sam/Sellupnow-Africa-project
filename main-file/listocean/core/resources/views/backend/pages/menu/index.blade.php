@extends('backend.admin-master')
@section('site-title')
    {{__('All Menus')}}
@endsection
@section('style')
    <x-datatable.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-9 col-lg-9">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('All Menus')}}</h2>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_dataTable">
                        <table class="dataTablesExample">
                            <thead>
                            <th>{{__('ID')}}</th>
                            <th>{{__('Title')}}</th>
                            <th>{{__('Status')}}</th>
                            <th>{{__('Created At')}}</th>
                            <th>{{__('Action')}}</th>
                            </thead>
                            <tbody>
                            @foreach($all_menu as $data)
                                <tr>
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->title}}</td>
                                    <td>
                                        @can('menu-edit')
                                            @if($data->status == 'default')
                                                <span class="alert alert-success">{{__('Default Menu')}}</span>
                                            @else
                                                <form action="{{route('admin.menu.default',$data->id)}}" method="post">
                                                    @csrf
                                                    <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5 set_default_menu">{{__('Set Default')}}</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                    <td>{{$data->created_at->format('d-M-Y')}}</td>
                                    <td>
                                    @can('menu-delete')
                                        @if($data->status != 'default')
                                            <x-popup.delete-popup :url="route('admin.menu.delete',$data->id)" />
                                        @endif
                                    @endcan
                                     @can('menu-edit')
                                      <x-icon.edit-icon :url="route('admin.menu.edit',$data->id)"/>
                                     @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Add New Menu')}}</h2>
                <form action="{{route('admin.menu.new')}}" method="POST" class="new_language_form">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="title" class="form__input__single__label">{{__('Title')}}</label>
                            <input class="form__control" name="title" id="title" placeholder="{{__('Title')}}">
                        </div>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Create Menu')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                            <input type="text" class="form__control radius-5" name="direction" id="edit_direction">
                        </div>
                        <div class="form__input__single">
                            <label for="edit_status" class="form__input__single__label">{{ __('Status') }}</label>
                            <input type="text" class="form__control radius-5" name="edit_status" id="edit_status">
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
    <script src="{{asset('assets/backend/js/jquery.nestable.js')}}"></script>
    <script>
        $(document).ready(function () {


            $('#nestable').nestable({
                group: 1,
                maxDepth:5
            }).on('change', function (e) {
            });

            function removeTags(str) {
                if ((str===null) || (str==='')){
                    return false;
                }
                str = str.toString();
                return str.replace( /(<([^>]+)>)/ig, '');
            }


            $(document).on('click','.add_mega_menu_to_menu',function (e) {
                e.preventDefault();

                var allList = $(this).parent().prev().find('input[type="checkbox"]:checked');
                var draggAbleMenuWrap = $('#nestable > ol');

                $.each(allList,function (index,value) {
                    $(this).attr('checked',false);
                    var draggAbleMenuLength = $('#nestable ol li').length + 1;
                    var allDataAttr = '';
                    var menuType = $(this).parent().parent().data('ptype');
                    var itemSelectMarkup = '';
                    allDataAttr += ' data-ptype="'+menuType+'"';
                    var randomID = Math.floor((Math.random() * 99999999) + 1);
                    var oldRandomId  = randomID;
                    var AjaxRandomId  = randomID;
                    draggAbleMenuWrap.append('<li class="dd-item" data-uniqueid="'+oldRandomId+'" data-id="'+draggAbleMenuLength+'" '+ allDataAttr +'>\n' +
                        ' <div class="dd-handle">'+$(this).parent().text()+'</div>\n' +
                        '<span class="remove_item">x</span>'+
                        '<span class="expand"><i class="ti-angle-down"></i></span>'+
                        '<div class="dd-body hide">' +
                        '<p>loading items...</p>'+
                        '</div>'+
                        '</li>');

                    $.ajax({
                        type: 'POST',
                        url: "{{route('admin.mega.menu.item.select.markup')}}",
                        data:{
                            _token: "{{csrf_token()}}",
                            type : menuType,
                            lang : $('select[name="lang"]').val(),
                            menu_id : $('#menu_id').val(),
                        },
                        success:function (data) {
                            var html = data;
                            setTimeout(function () {
                                $('li[data-uniqueid="'+AjaxRandomId+'"] .dd-body').html(html);
                            },1000);
                        }
                    });

                });

            });
            $(document).on('click','.add_page_to_menu',function (e) {
                e.preventDefault();
                //nestable
                var allList = $(this).parent().prev().find('input[type="checkbox"]:checked');
                var draggAbleMenuWrap = $('#nestable > ol');
                $.each(allList,function (index,value) {
                    $(this).attr('checked',false);
                    var draggAbleMenuLength = $('#nestable ol li').length + 1;
                    var allDataAttr = '';
                    var menuType = $(this).parent().parent().data('ptype');

                    if(menuType == 'static'){

                        var menuPslug = $(this).parent().parent().data('pslug');
                        var menuPname = $(this).parent().parent().data('pname');

                        allDataAttr += 'data-pname="'+menuPname+'"';
                        allDataAttr += ' data-pslug="'+menuPslug+'"';
                        allDataAttr += ' data-ptype="'+menuType+'"';

                    }else if(menuType == 'dynamic'){

                        var menuPid = $(this).parent().parent().data('pid');

                        allDataAttr += 'data-pid="'+menuPid+'"';
                        allDataAttr += ' data-ptype="'+menuType+'"';

                    }else if(menuType == 'custom'){

                        var menuPurl = $(this).parent().parent().data('purl');
                        var menuPName = $(this).parent().parent().data('pname');

                        allDataAttr += 'data-purl="'+menuPurl+'"';
                        allDataAttr += 'data-pname="'+menuPName+'"';
                        allDataAttr += ' data-ptype="'+menuType+'"';
                    }else{
                        var menuPid = $(this).parent().parent().data('pid');

                        allDataAttr += 'data-pid="'+menuPid+'"';
                        allDataAttr += ' data-ptype="'+menuType+'"';
                    }
                    draggAbleMenuWrap.append('<li class="dd-item" data-id="'+draggAbleMenuLength+'" '+ allDataAttr +'>\n' +
                        ' <div class="dd-handle">'+$(this).parent().text()+'</div>\n' +
                        '<span class="remove_item">x</span>'+
                        '<span class="expand"><i class="ti-angle-down"></i></span>'+
                        '<div class="dd-body hide">' +
                        '<input type="text" class="icon_picker" placeholder="eg: fas-fa-facebook"/>'+
                        '<input type="text" class="anchor_target" placeholder="eg: _target">'+
                        '<input type="text" class="menu_label" placeholder="eg: menu label" >'+
                        '</div>'+
                        '</li>');
                });
            });

            $(document).on('click','#add_custom_links',function (e) {
                e.preventDefault();

                var draggAbleMenuWrap = $('#nestable > ol');

                var draggAbleMenuLength = $('#nestable ol li').length + 1;

                var menuType = $(this).parent().parent().data('ptype');
                var menuName = $('#custom_label_text').val();//custom_label_text
                var menuSlug = $('#custom_url').val();//custom_url


                draggAbleMenuWrap.append('<li class="dd-item" data-id="'+draggAbleMenuLength+'" data-ptype="custom" data-purl="'+removeTags(menuSlug)+'" data-pname="'+removeTags(menuName)+'">\n' +
                    ' <div class="dd-handle">'+removeTags(menuName)+'</div>\n' +
                    '<span class="remove_item">x</span>'+
                    '<span class="expand"><i class="ti-angle-down"></i></span>'+
                    '<div class="dd-body hide"><input type="text" class="anchor_target" placeholder="eg: _blank"/><input type="text" class="icon_picker" placeholder="eg: fas-fa-facebook"/></div>'+
                    '</li>');
                $('#custom_label_text').val('');
                $('#custom_url').val('');
            });
            $(document).on('input','.menu_label',function (e) {
                var el = $(this);
                var value = el.val();

                if(value != '' ){
                    el.parent().parent().attr('data-menulabel',value);
                }else{
                    el.parent().parent().removeAttr('data-menulabel');
                }
            });
            $(document).on('input','.icon_picker',function (e) {
                var el = $(this);
                var value = el.val();

                if(value != '' ){
                    el.parent().parent().attr('data-icon',value);
                }else{
                    el.parent().parent().removeAttr('data-icon');
                }
            });
            $(document).on('input','.anchor_target',function (e) {
                var el = $(this);
                var value = el.val();

                if(value != '' ){
                    el.parent().parent().attr('data-antarget',value);
                }else{
                    el.parent().parent().removeAttr('data-antarget');
                }
            });
            $(document).on('input','.static_pname',function (e) {
                var el = $(this);
                var value = el.val();

                if(value != '' ){
                    el.parent().parent().attr('data-pname',value);
                }else{
                    el.parent().parent().removeAttr('data-pname');
                }
            });

            $(document).on('click', '.remove_item', function(e) {
                $(this).parent().remove();
            });

            $('body').on('change','select[name="items_id"]',function (e) {
                e.preventDefault();
                var el = $(this);
                var item_id = $(this).val();
                if(item_id != '' ){
                    el.parent().parent().attr('data-items_id',item_id);
                }else{
                    el.parent().parent().removeAttr('data-items_id');
                }
            });

            $(document).on('click','#menu_structure_submit_btn',function (e) {
                e.preventDefault();
                var alldata = $('#nestable').nestable('serialize');
                $('#menu_content').val(JSON.stringify(alldata));
                $(this).addClass("disabled");
                $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{__("Updating")}}');
                $('#menu_update_form').trigger("submit");
            });
        });
    </script>
@endsection
