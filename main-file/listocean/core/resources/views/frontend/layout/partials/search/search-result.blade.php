<div class="card" style="border:none">
    <div class="card-body">
        @if($listings->count() >0)
            @foreach($listings as $listing)
                <a href="{{ route('frontend.listing.details',$listing->slug) }}" class="suggestion-items">
                    <div class="search_thumb bg-image" {!! render_background_image_markup_by_attachment_id($listing->image,'','thumb') !!}></div>
                    <div class="text-part">
                        <span class="search-text-item oneLine"> {{ $listing->title }}</span>
                        <span class="home_listing_price"> {{ float_amount_with_currency_symbol($listing->price) }} </span>
                    </div>
                </a>
            @endforeach
        @else
            <p class="text-left text-warning">{{ __("Nothing Found") }}</p>
        @endif
    </div>
</div>
