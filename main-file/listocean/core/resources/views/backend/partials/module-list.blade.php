
<!-- Admin Manage Role Module -->
@if(auth()->guard('admin')->user()->role == 1)
<li  class="dashboard__bottom__list__item has-children @if (request()->is('admin/manage*')) active open show @endif">
    <a href="javascript:void(0)"> <i class="las la-user-cog"></i> {{ __('Admin Role Manage') }} </a>
    <ul class="submenu">
        <li class="dashboard__bottom__list__item @if (request()->is('admin/manage/create/new-admin')) selected @endif">
            <a href="{{ route('admin.create') }}"> {{ __('Add New Admin') }} </a>
        </li>
        <li class="dashboard__bottom__list__item @if (request()->is('admin/manage/all-admins')) selected @endif">
            <a href="{{ route('admin.all') }}"> {{ __('All Admins') }} </a>
        </li>
        <li class="dashboard__bottom__list__item @if (request()->is('admin/manage/permission/role/all')) selected @endif">
            <a href="{{ route('admin.role.create') }}"> {{ __('All Roles') }} </a>
        </li>
    </ul>
</li>
@endif

<!-- Country Manage Module -->
@canany(['country-list', 'country-csv-file-import', 'state-list', 'state-csv-file-import', 'city-list', 'city-csv-file-import'])
<li class="dashboard__bottom__list__item has-children @if(request()->is('admin/location/*')) active open @endif">
    <a href="javascript:void(0)"><i class="las la-globe"></i>
        <span class="icon_title">{{ __('Country Manage') }}</span>
    </a>
    <ul class="submenu" style="@if(request()->is('admin/location/*')) display:block; @endif">
        @can('country-list')
        <li class="dashboard__bottom__list__item @if(request()->is('admin/location/country/all-country')) selected @endif">
            <a href="{{ route('admin.country.all') }}">{{ __('All Countries') }}</a>
        </li>
        @endcan
        @can('country-csv-file-import')
        <li class="dashboard__bottom__list__item @if(request()->is('admin/location/country/csv/import')) selected @endif">
            <a href="{{ route('admin.country.import.csv.settings') }}">{{ __('Import Country') }}</a>
        </li>
        @endcan
        @can('state-list')
        <li class="dashboard__bottom__list__item @if(request()->is('admin/location/state/all-state')) selected @endif">
            <a href="{{ route('admin.state.all') }}">{{ __('All State') }}</a>
        </li>
        @endcan
        @can('state-csv-file-import')
        <li class="dashboard__bottom__list__item @if(request()->is('admin/location/state/csv/import')) selected @endif">
            <a href="{{ route('admin.state.import.csv.settings') }}">{{ __('Import States') }}</a>
        </li>
        @endcan
        @can('city-list')
        <li class="dashboard__bottom__list__item @if(request()->is('admin/location/city/all-city')) selected @endif">
            <a href="{{ route('admin.city.all') }}">{{ __('All Cities') }}</a>
        </li>
        @endcan
        @can('city-csv-file-import')
        <li class="dashboard__bottom__list__item @if(request()->is('admin/location/city/csv/import')) selected @endif">
            <a href="{{ route('admin.city.import.csv.settings') }}">{{ __('Import Cities') }}</a>
        </li>
        @endcan
    </ul>
</li>
@endcanany

<!-- Brand Module -->
@can('brand-list')
<li class="dashboard__bottom__list__item @if(request()->is('admin/brand/*')) active @endif">
    <a href="{{route('admin.brand.all')}}"><i class="las la-bars"></i>
        <span class="icon_title">{{ __('All Brands') }}</span>
    </a>
</li>
@endcan

<!-- Integration Module -->
@can('integration-list')
<li class="dashboard__bottom__list__item @if(request()->is('admin/integrations-manage*')) active @endif">
    <a href="{{route('admin.integration')}}"><i class="las la-puzzle-piece"></i>
        <span class="icon_title">{{ __('Integration') }}</span>
    </a>
</li>
@endcan

<!-- Newsletter Module -->
@canany(['newsletter-list'])
<li class="dashboard__bottom__list__item has-children @if(request()->is('admin/news-letter*')) active open @endif">
    <a href="javascript:void(0)"><i class="las la-envelope-open"></i>
        <span class="icon_title">{{ __('Newsletter Manage') }}</span>
    </a>
    <ul class="submenu" style="@if(request()->is('admin/news-letter*')) display:block; @endif">
        @can('newsletter-list')
            <li class="dashboard__bottom__list__item @if(request()->is('admin/news-letter')) selected @endif">
                <a href="{{ route('admin.newsletter.index') }}">{{ __('All Emails') }}</a>
            </li>
        @endcan
         @can('newsletter-list')
        <li class="dashboard__bottom__list__item @if(request()->is('admin/news-letter/all')) selected @endif">
            <a href="{{ route('admin.newsletter.mail') }}">{{ __('Email to All') }}</a>
        </li>
        @endcan
    </ul>
</li>
@endcanany

<!-- Blog Module -->
@canany(['blog-list', 'blog-add', 'blog-settings'])
<li class="dashboard__bottom__list__item has-children @if(request()->is('admin/blog/*')) active open @endif">
    <a href="javascript:void(0)"><i class="las la-rss"></i>
        <span class="icon_title">{{ __('Blog') }}</span>
    </a>
    <ul class="submenu" style="@if(request()->is('admin/blog/*')) display:block; @endif">
        @can('blog-list')
            <li class="dashboard__bottom__list__item @if(request()->is('admin/blog/all')) selected @endif">
                <a href="{{ route('admin.all.blog') }}">{{ __('Blog Manage') }}</a>
            </li>
        @endcan
        @can('blog-add')
            <li class="dashboard__bottom__list__item @if(request()->is('admin/blog/new')) selected @endif">
                <a href="{{ route('admin.blog.new') }}">{{ __('Add New Blog') }}</a>
            </li>
        @endcan
         @can('blog-settings')
            <li class="dashboard__bottom__list__item @if(request()->is('admin/blog/blog-details-settings')) selected @endif">
                <a href="{{ route('admin.blog.details.settings') }}">{{ __('Blog Details Settings') }}</a>
            </li>
        @endcan
    </ul>
</li>
@endcanany

<!-- Tags -->
@can('tag-list')
<li class="dashboard__bottom__list__item @if(request()->is('admin/tags')) active @endif">
    <a href="{{ route('admin.blog.tags') }}"><i class="las la-tags"></i>
        <span class="icon_title">{{ __('Tags') }}</span>
    </a>
</li>
@endcan

<!-- Support Ticket Module -->
@canany(['department-list', 'support-ticket-list'])
<li class="dashboard__bottom__list__item has-children @if(request()->is('admin/support-ticket/*')) active open @endif">
    <a href="javascript:void(0)"><i class="las la-headset"></i>
        <span class="icon_title">{{ __('Support') }}</span>
    </a>
    <ul class="submenu" style="@if(request()->is('admin/support-ticket/*')) display:block; @endif">
        @can('department-list')
        <li class="dashboard__bottom__list__item @if(request()->is('admin/support-ticket/department')) selected @endif">
            <a href="{{ route('admin.department') }}">{{ __('Department') }}</a>
        </li>
        @endcan
        @can('support-ticket-list')
            <li class="dashboard__bottom__list__item @if(request()->is('admin/support-ticket/tickets')) selected @endif">
                <a href="{{ route('admin.ticket') }}">{{ __('Support Ticket') }}</a>
            </li>
        @endcan
    </ul>
</li>
@endcanany

<!-- Pages Module -->
<li class="dashboard__bottom__list__item has-children @if(request()->is('admin/plugin-manage/*')) active open @endif">
    <a href="javascript:void(0)"><i class="las la-plug"></i>
        <span class="icon_title">{{ __('Plugins Manage') }}</span>
    </a>
    <ul class="submenu" style="@if(request()->is('admin/plugin-manage/*')) display:block; @endif">
        @can('plugins-list')
            <li class="dashboard__bottom__list__item @if(request()->is('admin/plugin-manage/all')) selected @endif">
                <a href="{{ route('admin.plugin.manage.all') }}">{{ __('All Plugins') }}</a>
            </li>
        @endcan
        @can('plugins-add')
            <li class="dashboard__bottom__list__item @if(request()->is('admin/plugin-manage/new')) selected @endif">
                <a href="{{ route('admin.plugin.manage.new') }}">{{ __('Add New Plugin') }}</a>
            </li>
        @endcan
    </ul>
</li>

@can('payment-currency-settings')
<li class="dashboard__bottom__list__item has-children @if(request()->is('admin/payment-settings/*') || request()->is('admin/payment-gateway/currency-settings')) active open @endif">
    <a href="javascript:void(0)"><i class="las la-money-check-alt"></i>
        <span class="icon_title">{{ __('Payment Gateway') }}</span>
    </a>
    <ul class="submenu" style="@if(request()->is('admin/payment-settings/*') || request()->is('admin/payment-gateway/currency-settings')) display:block; @endif">
        @can('payment-currency-settings')
            <li class="dashboard__bottom__list__item @if(request()->is('admin/payment-gateway/currency-settings')) selected @endif">
                <a href="{{ route('admin.payment.gateway.currency.settings') }}">{{ __('Currency Settings') }}</a>
            </li>
        @endcan
        @php
            $payment_gateways = \Modules\PaymentGateways\app\Models\PaymentGateway::pluck('name');
        @endphp
        @foreach($payment_gateways ?? [] as $gateway)
            <li class="dashboard__bottom__list__item @if(request()->is("admin/payment-settings/payment/{$gateway}")) selected @endif">
                <a class="text-capitalize" href="{{ route("admin.payment.settings.{$gateway}") }}">{{ __($gateway) }}</a>
            </li>
        @endforeach
    </ul>
</li>
@endcan

<!-- Render all module route start -->
@php
    $all_modules_route = (new \App\Helpers\ModuleMetaData())->getAllExternalMenu() ?? [];

@endphp
@foreach($all_modules_route as $index => $externalMenu)
    @php
        $flag = false;
        $activeRoutes = array_column((array) $externalMenu, 'route');
    @endphp

    @foreach ($externalMenu as $key => $individual_menu_item)
        @php
            $convert_to_array = (array) $individual_menu_item;
            $convert_to_array['label'] = __($convert_to_array['label']);
            if (array_key_exists('permissions', $convert_to_array) && !is_array($convert_to_array['permissions'])) {
                $convert_to_array['permissions'] = [$convert_to_array['permissions']];
            }
            $routeName = $convert_to_array['route'];
            $icon = array_key_exists('icon', $convert_to_array) ? $convert_to_array['icon'] : '';
        @endphp
        @if(count($externalMenu) > 1)
            @if($key === 0)
                <li class="dashboard__bottom__list__item has-children @if(in_array(\Request::route()->getName(), $activeRoutes)) active open @endif">
                    @endif

                    @if(empty($convert_to_array['parent']) && !$flag)
                        @php
                            $flag = true;
                        @endphp
                        <a href="javascript:void(0)">
                            <i class="{{$icon}}"></i>
                            <span class="icon_title">{{ $convert_to_array['label'] }} <span class="badge bg-danger">{{ __('Plugin') }}</span> </span>
                        </a>
                        <ul class="submenu" style=" @if(in_array(\Request::route()->getName(), $activeRoutes)) display:block; @endif">
                            @endif
                            @if($key !== 0 && $flag)
                                <li class="dashboard__bottom__list__item  @if(request()->routeIs($routeName) == $routeName) selected @endif">
                                    <a href="{{ route($routeName) }}">{{ $convert_to_array['label'] }}</a>
                                </li>
                            @endif
                            @if($key === count($externalMenu)-1)
                        </ul>
                </li>
            @endif
        @else
            <li class="dashboard__bottom__list__item @if(request()->routeIs($routeName)) active open @endif">
                <a href="{{ route($routeName) }}">  <i class="{{$icon}}"></i>  {{ $convert_to_array['label'] }} <span class="badge bg-danger">{{ __('Plugin') }}</span> </a>
            </li>
        @endif
    @endforeach
@endforeach
<!-- Render all module route end -->
