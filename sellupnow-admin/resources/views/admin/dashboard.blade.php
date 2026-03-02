@extends('layouts.app')

@section('header-title', __('Welcome Back,') . ' ' . Str::limit(auth()->user()?->name, 20))
@section('header-subtitle', __('Monitor your business analytics and statistics.'))

@section('content')

    <!-- Alert Box -->
    @if (app()->environment('local'))
        <div id="alertBox" class="alert alert-danger align-items-center gap-1 justify-content-between mb-3" role="alert" style="display: flex">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-bell"></i>
                <div>
                    <strong>{{ __('Note') }}</strong> {{ __('Every 3 hours all data will be cleared') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stat Boxes Row 1 -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-box item-4">
                        <h2 class="count">{{ $totalUsers }}</h2>
                        <h3 class="title">{{ __('Total Users') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/dashboard-customer.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-box item-1">
                        <h2 class="count">{{ $totalListings }}</h2>
                        <h3 class="title">{{ __('Total Listings') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/dashboard-product.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-box item-2">
                        <h2 class="count">{{ $activeListings }}</h2>
                        <h3 class="title">{{ __('Active Listings') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/dashboard-order.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-box item-3">
                        <h2 class="count">{{ $featuredListings }}</h2>
                        <h3 class="title">{{ __('Featured Ads') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/dashboard-shop.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Boxes Row 2 — Action Required -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('admin.listingModeration.index') }}" class="text-decoration-none">
                        <div class="dashboard-box item-1" style="border-left: 4px solid #f59e0b;">
                            <h2 class="count text-warning">{{ $pendingListings }}</h2>
                            <h3 class="title">{{ __('Pending Approval') }}</h3>
                            <div class="icon">
                                <img src="{{ asset('assets/icons-admin/clock.svg') }}" alt="icon" loading="lazy" />
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('admin.listingReport.index') }}" class="text-decoration-none">
                        <div class="dashboard-box item-3" style="border-left: 4px solid #ef4444;">
                            <h2 class="count text-danger">{{ $pendingReports }}</h2>
                            <h3 class="title">{{ __('Pending Reports') }}</h3>
                            <div class="icon">
                                <img src="{{ asset('assets/icons-admin/shopping-cart-times.svg') }}" alt="icon" loading="lazy" />
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('admin.identityVerification.index', ['status' => 'all']) }}" class="text-decoration-none">
                        <div class="dashboard-box item-2" style="border-left: 4px solid #3b82f6;">
                            <h2 class="count text-primary">{{ $totalVerifications }}</h2>
                            <h3 class="title">{{ __('ID Verifications') }}</h3>
                            <div class="icon">
                                <img src="{{ asset('assets/icons-admin/rotate-circle.svg') }}" alt="icon" loading="lazy" />
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="dashboard-box item-4">
                        <h2 class="count">{{ $activeAuctionBids }}</h2>
                        <h3 class="title">{{ __('Active Bids') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/chart-trend-up-green.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Summary -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="cardTitleBox">
                <h5 class="card-title chartTitle">{{ __('Platform Summary') }}</h5>
            </div>
            <div class="d-flex flex-wrap gap-3 orderStatus">
                <a href="{{ route('admin.listingModeration.index') }}" class="d-flex status flex-grow-1 pending">
                    <div class="d-flex align-items-center gap-2 justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/icons-admin/clock.svg') }}" alt="icon" loading="lazy" />
                            <span>{{ __('Pending Approval') }}</span>
                        </div>
                        <div class="icon"><img src="{{ asset('assets/icons-admin/arrow-export.svg') }}" alt="icon" loading="lazy" /></div>
                    </div>
                    <span class="count">{{ $pendingListings }}</span>
                </a>
                <a href="{{ route('admin.listingReport.index') }}" class="d-flex status flex-grow-1 cancelled">
                    <div class="d-flex align-items-center gap-2 justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/icons-admin/shopping-cart-times.svg') }}" alt="icon" loading="lazy" />
                            <span>{{ __('Pending Reports') }}</span>
                        </div>
                        <div class="icon"><img src="{{ asset('assets/icons-admin/arrow-export.svg') }}" alt="icon" loading="lazy" /></div>
                    </div>
                    <span class="count">{{ $pendingReports }}</span>
                </a>
                    <a href="{{ route('admin.identityVerification.index', ['status' => 'all']) }}" class="d-flex status flex-grow-1 processing">
                    <div class="d-flex align-items-center gap-2 justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/icons-admin/rotate-circle.svg') }}" alt="icon" loading="lazy" />
                            <span>{{ __('ID Verifications') }}</span>
                        </div>
                        <div class="icon"><img src="{{ asset('assets/icons-admin/arrow-export.svg') }}" alt="icon" loading="lazy" /></div>
                    </div>
                    <span class="count">{{ $totalVerifications }}</span>
                </a>
                <div class="d-flex status flex-grow-1 confirm">
                    <div class="d-flex align-items-center gap-2 justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/icons-admin/shopping-cart-check.svg') }}" alt="icon" loading="lazy" />
                            <span>{{ __('Active Listings') }}</span>
                        </div>
                    </div>
                    <span class="count">{{ $activeListings }}</span>
                </div>
                <div class="d-flex status flex-grow-1 onTheWay">
                    <div class="d-flex align-items-center gap-2 justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/icons-admin/chart-trend-up-green.svg') }}" alt="icon" loading="lazy" />
                            <span>{{ __('Featured Ads') }}</span>
                        </div>
                    </div>
                    <span class="count">{{ $featuredListings }}</span>
                </div>
                <div class="d-flex status flex-grow-1 delivered">
                    <div class="d-flex align-items-center gap-2 justify-content-between w-100">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/icons-admin/box-check.svg') }}" alt="icon" loading="lazy" />
                            <span>{{ __('Categories') }}</span>
                        </div>
                    </div>
                    <span class="count">{{ $totalCategories }}</span>
                </div>
            </div>
        </div>
    </div>

    <!---- Shop Wallet -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="cardTitleBox">
                <h5 class="card-title chartTitle">
                    {{ __('Admin Wallet') }}
                </h5>
            </div>

            <div class="row">
                <div class="col-lg-5">
                    <div class="wallet h-100">
                        <h3 class="balance">{{ showCurrency(auth()->user()?->wallet?->balance) }}</h3>
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap w-100">
                            <div>
                                <div class="d-flex align-items-center gap-1 percentUp">
                                    <span>+18.53%</span>
                                    <img src="{{ asset('assets/icons-admin/arrow.svg') }}" alt="icon" loading="lazy" />
                                </div>
                                <div class="title">{{ __('Total Earning') }}</div>
                            </div>
                            <div class="wallet-icon svg-bg">
                                <img src="{{ asset('assets/icons-admin/wallet.svg') }}" alt="" width="100%">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div class="amount">{{ showCurrency($alreadyWithdraw) }}</div>
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="title">{{ __('Already Withdraw') }}</div>
                                    <div class="icon svg-bg">
                                        <img src="{{ asset('assets/icons-admin/withdraw.svg') }}" alt="icon"
                                            loading="lazy" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div class="amount">{{ showCurrency($pendingWithdraw) }}</div>
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="title">{{ __('Pending Withdraw') }}</div>
                                    <div class="icon">
                                        <img src="{{ asset('assets/icons-admin/credit-card-orange.svg') }}"
                                            alt="icon" loading="lazy" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div class="amount">{{ showCurrency($totalCommission) }}</div>
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="title">{{ __('Total Commission') }}</div>
                                    <div class="icon">
                                        <img src="{{ asset('assets/icons-admin/chart-trend-up-green.svg') }}"
                                            alt="icon" loading="lazy" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div class="amount">{{ showCurrency($deniedWithdraw) }}</div>
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="title">{{ __('Rejected Withdraw') }}</div>
                                    <div class="icon">
                                        <img src="{{ asset('assets/icons-admin/withdraw-reject.svg') }}" alt="icon"
                                            loading="lazy" />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Listing Analytics -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="cardTitleBox d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title chartTitle mb-0">{{ __('Listing Analytics') }}</h5>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <button class="statisticsBtn " data-value="daily">
                            {{ __('Daily') }}
                        </button>
                        <button class="statisticsBtn" data-value="monthly">
                            {{ __('Monthly') }}
                        </button>
                        <button class="statisticsBtn active" data-value="yearly">
                            {{ __('Yearly') }}
                        </button>
                    </div>

                    <div class="statisticsDivder"></div>
                    <div>
                        <input type="date" name="date" id="dateStatistic" class="statisticsInput">
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary resetBtn">Reset</button>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col- col-lg-8">

                    <div class="card theme-dark">
                        <div class="card-body">
                            <div class="border-bottom pb-3">
                                <h3 id="totalListingsStat">{{ $totalListings }}</h3>
                                <p>{{ __('Total Listings') }}</p>
                            </div>
                            <canvas id="myChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="card h-100 border theme-dark">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="border-bottom pb-3">
                                <h3>{{ $totalUsers }}</h3>
                                <p>{{ __('User Overview') }}</p>
                            </div>

                            <div class="mt-auto colorDark">
                                <canvas id="myPieChart" width="200" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Recent Listings -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="cardTitleBox">
                <h5 class="card-title chartTitle">
                    {{ __('Recent Listings') }} <span style="color: #687387">({{ __('Latest 8') }})</span>
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table dashboard">
                    <thead>
                        <tr>
                            <th><strong>{{ __('Listing') }}</strong></th>
                            <th><strong>{{ __('Seller') }}</strong></th>
                            <th><strong>{{ __('Category') }}</strong></th>
                            <th><strong>{{ __('Price') }}</strong></th>
                            <th><strong>{{ __('Date') }}</strong></th>
                            <th><strong>{{ __('Status') }}</strong></th>
                            <th><strong>{{ __('Action') }}</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentListings as $listing)
                            <tr>
                                <td class="tableId">{{ Str::limit($listing->title ?? $listing->name ?? '—', 30) }}</td>
                                <td class="tablecustomer">{{ $listing->user?->name ?? '—' }}</td>
                                <td class="tableId">{{ $listing->category?->name ?? '—' }}</td>
                                <td class="tableId">{{ showCurrency($listing->price ?? 0) }}</td>
                                <td class="tableId">{{ $listing->created_at->format('d M, Y') }}</td>
                                <td class="tableStatus">
                                    <div class="statusItem">
                                        @if($listing->is_published && $listing->status)
                                            <div class="circleDot animatedDelivered"></div>
                                            <div class="statusText"><span class="statusDelivered">Active</span></div>
                                        @elseif(!$listing->is_published)
                                            <div class="circleDot animatedPending"></div>
                                            <div class="statusText"><span class="statusPending">Pending</span></div>
                                        @else
                                            <div class="circleDot animatedCancelled"></div>
                                            <div class="statusText"><span class="statusCancelled">Inactive</span></div>
                                        @endif
                                    </div>
                                </td>
                                <td class="tableAction">
                                    <a href="{{ route('admin.listingModeration.show', $listing->id) }}"
                                        data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="View listing"
                                        class="circleIcon btn-sm btn-outline-primary svg-bg">
                                        <img src="{{ asset('assets/icons-admin/eye.svg') }}" alt="icon" loading="lazy">
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">{{ __('No listings yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Recent Reports -->
        <div class="col-lg-4 col-12 mt-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="cardTitleBox d-flex align-items-center justify-content-between">
                        <h5 class="card-title chartTitle">{{ __('Recent Reports') }}</h5>
                        <a href="{{ route('admin.listingReport.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        @forelse ($recentReports as $report)
                            <a href="{{ route('admin.listingReport.show', $report->id) }}" class="customer-section">
                                <div class="customer-details">
                                    <div class="customer-image">
                                        <img src="{{ $report->user?->avatar ?? asset('default/default.jpg') }}" alt="user" loading="lazy"/>
                                    </div>
                                    <div class="customer-about">
                                        <p class="name text-dark">{{ Str::limit($report->listing?->title ?? $report->listing?->name ?? 'Listing', 28, '...') }}</p>
                                        <p class="order text-muted">{{ $report->user?->name ?? 'Anonymous' }}</p>
                                    </div>
                                </div>
                                <div class="border text-black px-2 py-1 flex-shrink-0" style="font-size: 12px; border-radius: 25px;">
                                    <span class="{{ $report->status === 'pending' ? 'text-warning' : 'text-success' }}">
                                        {{ ucfirst($report->status ?? 'pending') }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <p class="text-muted text-center py-3">{{ __('No reports yet') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Favourited Listings -->
        <div class="col-lg-4 col-12 mt-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="cardTitleBox">
                        <h5 class="card-title chartTitle">{{ __('Most Favourited Listings') }}</h5>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        @forelse ($topFavoritedListings as $listing)
                            <a href="{{ route('admin.listingModeration.show', $listing->id) }}" class="customer-section">
                                <div class="customer-details">
                                    <div class="customer-image">
                                        <img src="{{ $listing->thumbnail }}" alt="listing" loading="lazy"/>
                                    </div>
                                    <div class="customer-about">
                                        <p class="name text-dark">{{ Str::limit($listing->title ?? $listing->name ?? '—', 28, '...') }}</p>
                                        <p class="order">{{ showCurrency($listing->price ?? 0) }}</p>
                                    </div>
                                </div>
                                <div class="border text-black px-2 py-1 flex-shrink-0" style="font-size: 13px; border-radius: 25px;">
                                    <i class="bi bi-heart-fill text-danger"></i> {{ $listing->favorites_count }}
                                </div>
                            </a>
                        @empty
                            <p class="text-muted text-center py-3">{{ __('No listings yet') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- ID Verification Queue -->
        <div class="col-lg-4 col-12 mt-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="cardTitleBox d-flex align-items-center justify-content-between">
                        <h5 class="card-title chartTitle">{{ __('Verification Queue') }}</h5>
                        <a href="{{ route('admin.identityVerification.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                    <div class="text-center py-5">
                        <div style="font-size: 48px; color: var(--theme-color);">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h2 class="mt-2" style="font-size: 2.5rem; font-weight: 700; color: var(--theme-color);">{{ $pendingVerifications }}</h2>
                        <p class="text-muted">{{ __('Pending ID Verifications') }}</p>
                        @if($pendingVerifications > 0)
                            <a href="{{ route('admin.identityVerification.index') }}" class="btn btn-primary btn-sm mt-2">
                                {{ __('Review Now') }}
                            </a>
                        @else
                            <span class="badge bg-success">{{ __('All clear') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // get the value of --theme-color and --theme-hover-color
        var themeColor = "{{ $generaleSetting?->primary_color ?? '#EE456B' }}";
        var themeHoverColor = "{{ $generaleSetting?->secondary_color ?? '#FEE5E8' }}";

        var currentSitatics = '';
        var date = '';

        $('.statisticsBtn').on('click', function () {
            $('.statisticsBtn').removeClass('active');
            $(this).addClass('active');
            var sitatics = $(this).data('value');

            if (sitatics != currentSitatics) {
                currentSitatics = sitatics;
                fetchListingsChart();
            }
        });
        $('#dateStatistic').on('change', function () {
             date = $(this).val();
            if (date) {
                fetchListingsChart();
            }
        });
        $('.resetBtn').on('click', function () {
             date = '';
            $('#dateStatistic').val('');
            fetchListingsChart();
        });

        const fetchListingsChart = () => {
            $.ajax({
                url: "{{ route('admin.dashboard.listingStatistics') }}",
                method: 'GET',
                data: {
                    type: currentSitatics,
                    date: date
                },
                success: (response) => {
                   var chartLabels = response.data.labels;
                   var chartData = response.data.values;
                   loadChart(chartLabels, chartData);

                   $('#totalListingsStat').text(response.data.total);
                }
            });
        }

        fetchListingsChart();

        var isDarkMode = document.getElementById('appContent').classList.contains('app-theme-dark');
        var chartLabelColor = isDarkMode ? "#fff" : '#24262D';
        var chartBgColor = isDarkMode ? "#5a5a5b63" : themeHoverColor;

        const ctx = document.getElementById('myChart').getContext('2d');
        let myChart;

        function loadChart(chartLabels, chartData) {

            if (myChart) {
                myChart.destroy();
            }

            // Define your chart data
            const data = {
                labels: chartLabels,
                datasets: [{
                        type: 'bar',
                        label: 'Orders',
                        data: chartData,
                        backgroundColor: '#FAA7B5',
                        borderRadius: {
                            topLeft: 12,
                            topRight: 12,
                            bottomLeft: 0,
                            bottomRight: 0
                        },
                        borderColor: themeHoverColor,
                        borderSkipped: false
                    },
                    {
                        type: 'line',
                        label: 'Orders',
                        data: chartData,
                        borderColor: themeColor,
                        backgroundColor: chartBgColor,
                        fill: true,
                        tension: 0.5,
                        pointBackgroundColor: 'white',
                        pointBorderColor: 'rgba(255, 99, 132, 1)',
                        pointRadius: 5
                    }
                ]
            };

            // Chart configuration
            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: false,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            };

            // Initialize the chart
            myChart = new Chart(ctx, config);
        }

        const labelsData = ["{{ __('Active') }}", "{{ __('Pending') }}", "{{ __('Featured') }}"];
        const chartData = ["{{ $activeListings }}", "{{ $pendingListings }}", "{{ $featuredListings }}"];
        const chartDataBg = ['#EE456B', '#f59e0b', '#067BFF'];

        // customer, shop, rider chart
        const cutOut = document.getElementById('myPieChart').getContext('2d');
        new Chart(cutOut, {
            type: 'doughnut',
            data: {
                labels: labelsData,
                datasets: [{
                    data: chartData,
                    backgroundColor: chartDataBg,
                    hoverOffset: 4,
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '50%',
                rotation: -90,
                circumference: 180,
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 14,
                            font: {
                                size: 14
                            },
                            color: chartLabelColor,
                            padding: 20
                        }
                    }
                },
            }
        });

        // Hide the alert box after 5 seconds
        const hideAlert = () => {
            setTimeout(() => {
                $('#alertBox').slideUp();
            }, 5000);

            setTimeout(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }, 100);
        }
        hideAlert();
    </script>
    @if ($flashSale ?? false)
        <script>
            var startDateAndTime = "{{ ($flashSale ?? null)?->start_date }}T{{ ($flashSale ?? null)?->start_time }}";
        </script>
    @endif
@endpush
@push('css')
    <style>
        /* Dashboard stat link hover */
        a.dashboard-box { text-decoration: none; }
        a.dashboard-box:hover { opacity: 0.9; }
    </style>
@endpush
