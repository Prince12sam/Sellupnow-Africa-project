<div class="relevant-ads box-shadow1">
    <h4 class="disTittle">{{ get_static_option('listing_relevant_title') ?? __('Relevant Ads') }}</h4>
    <div class="add-wraper relevant-listing-wrapper">
       @include('frontend.pages.listings.relevant-markup')
    </div>
    <div class="text-center mt-3">
        @if ($related_listings->isEmpty())
            <div class="btn-wrapper">
                <button class="cmn-btn2 transparent-btn" disabled>{{ __('No More Relevant Items') }}</button>
            </div>
        @else
            <button id="load-more-ads" class="cmn-btn2 red-btn" data-listing-id="{{ $listing->id }}">{{ __('Load More') }}</button>
        @endif
    </div>
</div>

{{-- Ad slot: under related/relevant listings --}}
<x-ads.ad-slot placement="listing_details_under_related" :listing-id="$listing->id" class="mt-3" />
