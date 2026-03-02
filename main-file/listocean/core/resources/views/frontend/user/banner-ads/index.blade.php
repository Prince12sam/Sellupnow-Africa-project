@extends('frontend.layout.master')
@section('site_title') {{ __('My Banner Ads') }} @endsection
@section('content')
<div class="profile-setting profile-pages section-padding2">
    <div class="container-1920 plr1">
        <div class="row">
            <div class="col-12">
                <div class="profile-setting-wraper">
                    @include('frontend.user.layout.partials.user-profile-background-image')
                    <div class="down-body-wraper">
                        @include('frontend.user.layout.partials.sidebar')
                        <div class="main-body">
                            <x-frontend.user.responsive-icon/>

                            <div class="relevant-ads box-shadow1 p-24">
                                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                                    <h4 style="font-weight:700;">{{ __('My Banner Ads') }}</h4>
                                    <a href="{{ route('user.banner-ads.create') }}" class="cmn-btn">
                                        <i class="las la-plus me-1"></i>{{ __('Submit New Banner Ad') }}
                                    </a>
                                </div>

                                @if($ads->count())
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead style="background:#f8fafc;">
                                                <tr>
                                                    <th>{{ __('Title') }}</th>
                                                    <th>{{ __('Requested Slot') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Submitted') }}</th>
                                                    <th>{{ __('Preview') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($ads as $ad)
                                                <tr>
                                                    <td style="font-weight:600;">{{ $ad->title }}</td>
                                                    <td>
                                                        <code style="font-size:11px;background:#f1f5f9;padding:2px 6px;border-radius:4px;">
                                                            {{ $ad->requested_slot ?? $ad->slot ?? '—' }}
                                                        </code>
                                                    </td>
                                                    <td>
                                                        @if($ad->status == 1)
                                                            <span class="badge bg-success">{{ __('Approved') }}</span>
                                                        @elseif($ad->status == 2)
                                                            <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">{{ __('Pending Review') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted" style="font-size:12px;">
                                                        {{ $ad->created_at->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        @if(!empty($ad->image))
                                                            <img src="{{ get_image_url_id_wise($ad->image) }}"
                                                                 alt="{{ $ad->title }}"
                                                                 style="height:40px;max-width:120px;object-fit:contain;border-radius:4px;border:1px solid #e2e8f0;"
                                                                 loading="lazy">
                                                        @else
                                                            <span class="text-muted" style="font-size:12px;">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">{{ $ads->links() }}</div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="las la-ad" style="font-size:48px;color:#cbd5e1;"></i>
                                        <p class="text-muted mt-2">{{ __('You have not submitted any banner ads yet.') }}</p>
                                        <a href="{{ route('user.banner-ads.create') }}" class="cmn-btn mt-2">
                                            {{ __('Submit Your First Ad') }}
                                        </a>
                                    </div>
                                @endif
                            </div>

                        </div>{{-- /main-body --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
