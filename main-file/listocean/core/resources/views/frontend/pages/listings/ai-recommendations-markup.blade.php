{{-- AI-Powered Recommendations Markup --}}
{{-- Used by AiRecommendationController::recommend() --}}
@foreach($recommendations as $listing)
    <div class="single-add-card">
        <div class="single-add-image">
            <a href="{{ route('frontend.listing.details', $listing->slug) }}">
                {!! render_image_markup_by_attachment_id($listing->image) !!}
            </a>
        </div>
        <div class="single-add-body">
            <h4 class="add-heading head4">
                <a href="{{ route('frontend.listing.details', $listing->slug) }}">{{ $listing->title }}</a>
            </h4>
            <div class="btn-wrapper">
                @if($listing->is_featured === 1)
                    <span class="pro-btn2">
                        <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 0V3.88889H7L3 10V6.11111H0L4 0Z" fill="white"/>
                        </svg>
                        {{ __('FEATURED') }}
                    </span>
                @endif
            </div>
            <div class="pricing head4">{{ float_amount_with_currency_symbol($listing->price) }}</div>
            <x-listings.listing-location :listing="$listing"/>
        </div>
    </div>
@endforeach
