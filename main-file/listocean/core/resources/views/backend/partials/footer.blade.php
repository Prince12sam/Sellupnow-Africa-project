<script src="{{asset('assets/common/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('assets/common/js/jquery-migrate-3.4.1.min.js')}}"></script>
<script src="{{asset('assets/common/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/backend/js/slick.js')}}"></script>
<script src="{{asset('assets/backend/js/plugin.js')}}"></script>
<script src="{{asset('assets/backend/js/fancybox.umd.js')}}"></script>
<script src="{{asset('assets/backend/js/main.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<x-backend.password-show-hide-js/>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<x-btn.custom :id="'update'" :title="__('Submitting')" />

@yield('scripts')

<x-popup.default-js-popup/>
