@extends('backend.admin-master')
@section('site-title')
    {{__('Check Update')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="col-12 mt-5">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__("Check Update")}}</h2>
                    <x-validation.error/>
                        <button type="button" class="cmnBtn btn_5 btn_bg_blue radius-5" id="click_for_check_update">
                            <i class="fas fa-spinner fa-spin d-none"></i> {{__('Click to check For Update')}}
                        </button>
                        <div id="update_notice_wrapper" class="d-none text-center">

                       </div>
                </div>
            </div>
        </div>
  <x-media.markup/>
@endsection
@section('scripts')
    <script>
        (function($){
            "use strict";
            $(document).ready(function() {
                //write code
                $("body").on("click","#update_download_and_run_update",function (e){
                    e.preventDefault();
                    var el = $(this);
                    el.children().removeClass('d-none');
                    if(el.attr("disabled") != undefined && el.attr("disabled") === "disabled"){
                        return;
                    }
                    el.attr("disabled",true);
                    $.ajax({
                        url: el.attr("data-action"),
                        type: "POST",
                        data: {
                            _token : "{{csrf_token()}}",
                            version: el.attr("data-version")
                        },
                        success: function (data){
                            el.children().addClass('d-none');
                            if(data.msg != undefined && data.msg != ""){
                                el.text(data.msg).removeClass("btn-warning").addClass("btn-"+data.type);
                            }
                        },
                        error: function (error) {
                        }
                    });
                });

                $(document).on("click","#click_for_check_update",function (e){
                    e.preventDefault();
                    var el = $(this);
                    el.children().removeClass('d-none');
                    el.attr("disabled",true);
                    $.ajax({
                        url: "{{route('admin.general.update.version.check')}}",
                        type: "GET",
                        success: function (data){
                            el.children().addClass('d-none');
                            if(data.markup != ""){
                                $("#update_notice_wrapper").append(data.markup);
                            }else if(data.msg != ""){
                                $("#update_notice_wrapper").append("<div class='alert alert-"+data.type+"'>"+data.msg+"</div>");
                            }
                            $("#update_notice_wrapper").removeClass('d-none');
                            el.hide();
                        },
                        error: function (error) {
                        }
                    });
                });
            });
        }(jQuery));
    </script>
@endsection
