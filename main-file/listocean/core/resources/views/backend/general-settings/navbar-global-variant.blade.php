@extends('backend.admin-master')
@section('site-title')
    {{__('Navbar Global Vartiant Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="col-12 mt-5">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__("Navbar Global Variants Settings")}}</h2>
                    <x-validation.error/>
                    <form action="{{route('admin.general.global.variant.navbar')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form__input__single">
                            <input type="hidden" class="form-control" id="global_navbar_variant" value="{{ get_static_option('global_navbar_variant') }}" name="global_navbar_variant">
                        </div>
                        <div class="row">
                            @for($i = 1; $i < 3; $i++)
                                <div class="col-lg-6 col-md-6">
                                    <div class="img-select @if($i === 1) selected @endif">
                                        <div class="img-wrap">
                                            <img src="{{asset('assets/backend/variant-images/navbar/'.$i.'.jpg')}}" data-home_id="0{{$i}}" alt="">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <div class="btn_wrapper mt-4">
                            <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                var imgSelect = $('.img-select');
                var id = $('#global_navbar_variant').val();
                imgSelect.removeClass('selected');
                $('img[data-home_id="'+id+'"]').closest('.img-select').addClass('selected');
                $(document).on('click', '.img-select', function (e) {
                    e.preventDefault();
                    imgSelect.removeClass('selected');
                    $(this).addClass('selected');
                    $('#global_navbar_variant').val($(this).find('img').data('home_id'));
                });
            });
        }(jQuery));
    </script>
@endsection
