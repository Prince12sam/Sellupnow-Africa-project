@extends('backend.admin-master')
@section('site-title')
    {{__('Sitemap Settings')}}
@endsection
@section('style')
    <x-datatable.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Sitemap Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.general.sitemap.settings')}}" id="sitemap_form" method="post">
                    @csrf
                    <div class="form__input__single">
                        <input type="hidden" class="site_url_data" name="site_url" value="{{url('/')}}">
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5 sitemap_button">{{ __('Generate Now') }}</button>
                    </div>
                    <br>
                    <small class="text-danger">{{__('It will take time to generate sitemap..Please increase your server executing time over ( 300 seconds )')}}</small>
                </form>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_dataTable">
                        <table class="dataTablesExample">
                            <thead>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Date')}}</th>
                            <th>{{__('Size')}}</th>
                            <th>{{__('Action')}}</th>
                            </thead>
                            <tbody>
                            @foreach($all_sitemap as $data)
                                <tr>
                                    <td>{{basename($data)}}</td>
                                    <td>{{date('j F Y - h:m:s',filectime($data)) }}</td>
                                    <td>@if(trim(formatBytes(filesize($data))) === 'NAN') {{__('0 Byte')}} @else {{formatBytes(filesize($data))}} @endif</td>
                                    <td>
                                        <a class="btn btn-xs text-white btn-danger mb-3 mr-1 delete_sitemap_xml_file_btn">
                                            <i class="ti-trash"></i>
                                        </a>
                                        <form method='post' class="d-none delete_sitemap_file_form"  action='{{route("admin.general.sitemap.settings.delete")}}'>
                                            @csrf
                                            <input type='hidden' name='sitemap_name' value='{{$data}}'>
                                            <input type='submit' class='btn btn-danger btn-xs' value='{{__('Yes, Please')}}'>
                                        </form>
                                        <a href="{{asset('sitemap')}}/{{basename($data)}}" download class="btn btn-primary btn-xs mb-3 mr-1"> <i class="fa fa-download"></i> </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
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
            $(document).on('click','.delete_sitemap_xml_file_btn',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to delete it?")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete It!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next('.delete_sitemap_file_form').find('input[type="submit"]').trigger('click');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
