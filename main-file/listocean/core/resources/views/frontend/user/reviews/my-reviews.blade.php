@extends('frontend.layout.master')
@section('site_title')
    {{ __('My Reviews') }}
@endsection
@section('style')
    <style>
        .review-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        .review-card .reviewer-img img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .review-card .reviewer-img .no-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #94a3b8;
        }
        .review-card .reviewer-body { flex: 1; }
        .review-card .reviewer-name { font-weight: 600; font-size: 15px; margin-bottom: 2px; }
        .review-card .review-date { font-size: 12px; color: #94a3b8; margin-bottom: 6px; }
        .review-card .review-message { color: #475569; font-size: 14px; line-height: 1.6; margin-bottom: 8px; }
        .star-rating { color: #f59e0b; font-size: 14px; }
        .star-rating .empty { color: #d1d5db; }
        .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
        .empty-state svg { margin-bottom: 16px; opacity: .4; }
    </style>
@endsection
@section('content')
    <div class="profile-setting my-reviews section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-frontend.user.responsive-icon/>
                                <div class="relevant-ads all-listings box-shadow1">
                                    <h4 class="dis-title">{{ __('My Reviews') }}
                                        <span class="badge bg-secondary ms-2" style="font-size:13px;">{{ $reviews->total() }}</span>
                                    </h4>

                                    @if($reviews->count() > 0)
                                        @foreach($reviews as $review)
                                            <div class="review-card">
                                                <div class="reviewer-img">
                                                    @if(!empty($review->reviewer?->image))
                                                        {!! render_image_markup_by_attachment_id($review->reviewer->image,'','thumb') !!}
                                                    @else
                                                        <div class="no-img">
                                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="reviewer-body">
                                                    <div class="reviewer-name">
                                                        {{ $review->reviewer?->first_name }} {{ $review->reviewer?->last_name }}
                                                    </div>
                                                    <div class="review-date">{{ $review->created_at->diffForHumans() }}</div>
                                                    <div class="star-rating mb-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->rating)
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                            @else
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#d1d5db" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                            @endif
                                                        @endfor
                                                        <span class="ms-1" style="font-size:12px;color:#64748b;">{{ $review->rating }}/5</span>
                                                    </div>
                                                    <div class="review-message">{{ $review->message }}</div>
                                                    @if(!empty($review->reviewer?->username))
                                                        <a href="{{ route('about.user.profile', $review->reviewer->username) }}"
                                                           class="btn btn-sm btn-outline-secondary" style="font-size:12px;border-radius:8px;">
                                                            {{ __('View Profile') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="mt-3">
                                            {{ $reviews->links() }}
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <h5>{{ __('No reviews yet') }}</h5>
                                            <p>{{ __('Reviews from other users will appear here.') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
