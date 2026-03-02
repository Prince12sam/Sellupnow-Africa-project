@if($listings->count() >0)
    @foreach($listings as $listing)
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

                <div class="dates">
                    <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 7.83333L7 6.5V3.16667M1 6.5C1 7.28793 1.15519 8.06815 1.45672 8.7961C1.75825 9.52405 2.20021 10.1855 2.75736 10.7426C3.31451 11.2998 3.97595 11.7417 4.7039 12.0433C5.43185 12.3448 6.21207 12.5 7 12.5C7.78793 12.5 8.56815 12.3448 9.2961 12.0433C10.0241 11.7417 10.6855 11.2998 11.2426 10.7426C11.7998 10.1855 12.2417 9.52405 12.5433 8.7961C12.8448 8.06815 13 7.28793 13 6.5C13 5.71207 12.8448 4.93185 12.5433 4.2039C12.2417 3.47595 11.7998 2.81451 11.2426 2.25736C10.6855 1.70021 10.0241 1.25825 9.2961 0.956723C8.56815 0.655195 7.78793 0.5 7 0.5C6.21207 0.5 5.43185 0.655195 4.7039 0.956723C3.97595 1.25825 3.31451 1.70021 2.75736 2.25736C2.20021 2.81451 1.75825 3.47595 1.45672 4.2039C1.15519 4.93185 1 5.71207 1 6.5Z" stroke="#64748B" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    @if($listing->published_at !== null)
                        @php
                            $publishedAt = \Carbon\Carbon::parse($listing->published_at);
                        @endphp
                        <span>{{ $publishedAt->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            <x-listings.favorite-item-add-remove :favorite="$listing->id ?? 0" />
        </div>
        <div class="devider"></div>
    @endforeach
@else

@endif
