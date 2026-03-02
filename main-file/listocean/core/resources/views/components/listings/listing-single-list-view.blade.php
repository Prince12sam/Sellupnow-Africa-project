@if($listings->count() >0)
    @foreach($listings as $listing)
        <div class="singleFeatureCard">
            <div class="featureImg">
                <x-listings.favorite-item-add-remove :favorite="$listing->id ?? 0" />
                <a href="{{ route('frontend.listing.details', $listing->slug) }}" class="main-card-image">
                    {!! render_image_markup_by_attachment_id($listing->image, '','','thumb'); !!}
                </a>
            </div>
            <div class="featurebody">
                <h4> <a href="{{ route('frontend.listing.details', $listing->slug) }}" class="featureTittle head4 twoLine">{{ $listing->title }}</a> </h4>

                <x-listings.listing-location :listing="$listing"/>

                <div class="btn-wrapper">
                    @if($listing->is_featured === 1)
                        <span class="pro-btn2">
                            <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M4 0V3.88889H7L3 10V6.11111H0L4 0Z" fill="white"/>
                            </svg>  {{ __('FEATURED') }}
                        </span>
                    @endif
                </div>

                <span class="featurePricing d-flex justify-content-between align-items-center">
                <span class="money">{{ amount_with_currency_symbol($listing->price) }}</span>
                <span class="date">
                    @if(!empty($listing->published_at))
                        {{ \Carbon\Carbon::parse($listing->published_at)->diffForHumans() }}
                    @endif
                </span>
            </span>
            </div>
        </div>
    @endforeach
@else
    <p>{{ __('No listings found.') }}</p>
@endif
