@extends('backend.admin-master')
@section('site-title')
    {{__('Dashboard')}}
@endsection
@section('style')
    <style>
        .order_id img{
            width: 50px !important;
        }
        .table_customer__thumb img {
            height: 60px;
        }
    </style>
@endsection
@section('content')
    <div class="dashboard__body posPadding">
        <div class="dashboard__inner">
            <div class="dashboard__inner__item">
                <div class="dashboard__inner__item__flex">
                    <div class="dashboard__inner__item__left bodyItemPadding">
                        <div class="dashboard__inner__header">
                            <div class="dashboard__inner__header__flex">
                                <div class="dashboard__inner__header__left">
                                    <h4 class="dashboard__inner__header__title"> <strong id="greeting"></strong>, {{ optional(Auth::guard('admin')->user())->name ?? '' }} </h4>
                                    <p class="dashboard__inner__header__para">{{ __('Manage your dashboard here') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard_promo">
                            <div class="row g-4 mt-2">
                                @if(!empty($dashboardData))
                                    @foreach($dashboardData as $item)
                                    <div class="col-xxl-2 col-xl-3 col-sm-6">
                                        <div class="dashboard_promo__single style_02 bg__white radius-10 padding-20">
                                            <span class="dashboard_promo__single__subtitle d-flex justify-content-between align-items-center">
                                                <span>
                                                {{ $item['title'] ?? '' }}
                                                 </span>
                                                @if(isset($item['route']))
                                                    <a href="{{ route($item['route']) }}">
                                                        <i class="las la-arrow-right fs-3 font-weight-600"></i>
                                                    </a>
                                                @endif
                                            </span>
                                            <h4 class="dashboard_promo__single__price mt-2">{{ $item['value'] ?? 0 }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row g-4 mt-1">
                            <div class="col-lg-6">
                                <div class="dashboard__card bg__white padding-20 radius-10">
                                    <h5 class="dashboard__card__header__title">{{ __('Recent Users') }}</h5>
                                    <div class="dashboard__card__inner border_top_1">
                                        <div class="dashboard__inventory__table custom_table">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th>{{ __('ID') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Created On') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(!empty($recent_users))
                                                    @foreach($recent_users as $user)
                                                    <tr class="table_row">
                                                        <td><span class="order_id">{{ $user->id }}</span></td>
                                                        <td>
                                                            <div class="table_customer">
                                                                <div class="table_customer__flex">
                                                                    <div class="table_customer__thumb">
                                                                        @if(!empty($user->image))
                                                                            {!! render_image_markup_by_attachment_id($user->image) !!}
                                                                        @else
                                                                            <img src="{{ asset('assets/frontend/img/static/user-no-image.webp') }}" alt="No Image">
                                                                        @endif
                                                                    </div>
                                                                    <div class="table_customer__contents">
                                                                        <h6 class="table_customer__title">{{ $user->fullname }}</h6>
                                                                        <p class="table_customer__para mt-1">{{ $user->email }}</p>
                                                                        <p class="table_customer__para mt-1">{{ $user->phone }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span class="table_date">{{ $user->created_at->format('d M Y') }}</span></td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="dashboard__card bg__white padding-20 radius-10">
                                    <h5 class="dashboard__card__header__title">{{ __('Recent Listings') }}</h5>
                                    <div class="dashboard__card__inner border_top_1">
                                        <div class="dashboard__inventory__table custom_table">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th>{{ __('ID') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Title') }}</th>
                                                    <th>{{ __('Image') }}</th>
                                                    <th>{{ __('Details') }}</th>
                                                    <th>{{ __('Created On') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(!empty($recent_listings))
                                                    @foreach($recent_listings as $listing)
                                                    <tr class="table_row">
                                                        <td><span class="order_id">{{ $listing->id }}</span></td>
                                                        <td>
                                                            <div class="table_customer">
                                                                <div class="table_customer__flex">
                                                                    <div class="table_customer__thumb">
                                                                        @if(!empty(optional($listing?->user)->image))
                                                                            {!! render_image_markup_by_attachment_id(optional($listing?->user)->image) !!}
                                                                        @else
                                                                            <img src="{{ asset('assets/frontend/img/static/user-no-image.webp') }}" alt="No Image">
                                                                        @endif
                                                                    </div>
                                                                    <div class="table_customer__contents">
                                                                        <h6 class="table_customer__title">{{ $listing?->user?->fullname }}</h6>
                                                                        <p class="table_customer__para mt-1">{{ $listing?->user?->email }}</p>
                                                                        <p class="table_customer__para mt-1">{{ $listing?->user?->phone }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.listings.details', $listing->id) }}">
                                                            <span class="order_id">{{ $listing->title }}</span>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="order_id">
                                                                {!! render_image_markup_by_attachment_id($listing->image) !!}
                                                            </span>
                                                            </td>
                                                        <td>
                                                            <a href="{{ route('admin.listings.details', $listing->id) }}" class="cmnBtn btn_5 btn_bg_info btnIcon radius-5">
                                                                <i class="las la-eye"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="table_date">{{ $listing->created_at->format('d M Y') }}</span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mt-0">
                            <div class="col-xl-6 col-lg-6">
                                <div class="dashboard__card bg__white padding-20 radius-10">
                                    <div class="dashboard__card__header">
                                        <div class="dashboard__card__header__flex">
                                            <div class="dashboard__card__header__left">
                                                <h5 class="dashboard__card__header__title">{{ __('Customers') }}
                                                    <p>{{ __('Total Users:') }} {{ $total_user ?? 0 }}</p>
                                                </h5>
                                            </div>
                                            <div class="dashboard__card__header__right">
                                                <select id="timeIntervalSelect" class="select2_activation">
                                                    @foreach(['Weekly', 'Monthly', 'Yearly', 'Daily', 'Hourly'] as $key => $option)
                                                        <option value="{{ $key }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chart__item__inner mt-4">
                                        <canvas id="lineChartCustomer"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="dashboard__card bg__white padding-20 radius-10">
                                    <div class="dashboard__card__header">
                                        <div class="dashboard__card__header__flex">
                                            <div class="dashboard__card__header__left">
                                                <h5 class="dashboard__card__header__title">{{ __('Listings') }}
                                                    <p>{{ __('Total Listings:') }} {{ $total_listings ?? 0 }}</p>
                                                </h5>
                                            </div>
                                            <div class="dashboard__card__header__right">
                                                <select id="listingTimeIntervalSelect" class="select2_activation">
                                                    @foreach(['Weekly', 'Monthly', 'Yearly', 'Daily', 'Hourly'] as $key => $option)
                                                        <option value="{{ $key }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chart__item__inner mt-4">
                                        <canvas id="lineChartListings"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!empty($visitors) && $visitors->count() > 0)
                        <div class="row g-4 mt-1">
                            <div class="col-xxl-12 col-lg-12">
                                <div class="dashboard__card bg__white padding-20 radius-10">
                                    <div class="dashboard__card__header">
                                        <h4 class="dashboard__card__header__title">{{ __('Ads Visitors by Country') }}</h4>
                                    </div>
                                    <div class="chart__item__inner mt-4">
                                        <div class="countryMap__wrapper">
                                            <div class="row gy-4 justify-content-center">
                                                <div class="col-md-8">
                                                    <div class="countryMap__wrapper__map">
                                                        <div class="countryMap">
                                                            <div class="map"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="countryMap__wrapper__progress radius-10" style="max-height: 400px; overflow-y: auto;">
                                                        @foreach($visitors as $visitor)
                                                            <div class="progress__item">
                                                                <div class="progress__item__flex">
                                                                    <span class="progress__item__title">{{ $visitor->country }} <b class="views">{{ $visitor->total }}</b></span>
                                                                    <span class="progress__item__bars__percent">{{ round(($visitor->total / $visitors->sum('total')) * 100) }}%</span>
                                                                </div>
                                                                <div class="progress__item__bars">
                                                                    <div class="progress__item__main" data-percent="{{ round(($visitor->total / $visitors->sum('total')) * 100) }}"></div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            let currentTime = new Date().getHours();
            let morningGreeting = "{{ __('Good Morning') }}";
            let afternoonGreeting = "{{ __('Good Afternoon') }}";
            let eveningGreeting = "{{ __('Good Evening') }}";
            if (currentTime >= 0 && currentTime < 12) {
                $('#greeting').text(morningGreeting);
            } else if (currentTime >= 12 && currentTime < 18) {
                $('#greeting').text(afternoonGreeting);
            } else {
                $('#greeting').text(eveningGreeting);
            }
        });
    </script>
    @include('backend.pages.dashboard.line-graph-js')
     @if(!empty($visitors) && $visitors->count() > 0)
         @include('backend.pages.dashboard.visitors-by-country-js')
     @endif
@endsection
