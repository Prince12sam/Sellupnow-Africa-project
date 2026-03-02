@php
    $footer_variant = !is_null(get_footer_style()) ? get_footer_style() : '02';
@endphp
@include('frontend.layout.partials.footer-variant.footer-'.$footer_variant)

@include('frontend.layout.partials.js.basic-markup')

<script src="{{ asset('assets/common/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/frontend/js/plugin.js') }}"></script>
<script src="{{ asset('assets/frontend/js/main.js') }}"></script>
<script src="{{asset('assets/frontend/js/dynamic-script.js')}}"></script>
<script src="{{ asset('assets/common/js/toastr.min.js') }}"></script>
{!! Toastr::message() !!}
<script src="{{asset('assets/backend/js/select2.min.js')}}"></script>
@include('frontend.layout.partials.js.common-js')
@include('frontend.layout.partials.gdpr-cookie')

@if(moduleExists("Membership"))
    @if(membershipModuleExistsAndEnable("Membership"))
        <x-payment.payment-gateway-js/>
        <x-membership.membership-js/>
    @endif
@endif

<x-listings.listing-favorite-js/>
@include('frontend.pages.listings.listings-search-js')
@include('frontend.layout.partials.search.home-search-js')
@include('frontend.layout.partials.js.toastr-js')
@include('frontend.layout.partials.js.slick-slider-configuration-js')
@include('frontend.layout.partials.js.newsletter-js')
@if(!empty(get_static_option('google_map_settings_on_off')))
    @include('frontend.layout.partials.search.google-map-search-js')
@endif

<!--page script-->
<x-frontend.advertisement-script/>
<x-btn.frontend-update-btn/>

@yield('scripts')

{!! renderBodyEndHooks() !!}
</body>
</html>
