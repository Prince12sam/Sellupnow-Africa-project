@php
    $isAuthenticated = auth()->guard('web')->check();
    $isFavorite = $isAuthenticated ? \App\Models\Frontend\ListingFavorite::where('user_id', auth()->guard('web')->user()->id)
                   ->where('listing_id', $favorite)->exists() : false;
    $iconClass = $isFavorite ? 'favorite_remove_icon' : 'favorite_add_icon';

    $user_add_item = '';
    if ($isFavorite){
        $user_add_item = $isFavorite ? 'favourite' : '';
    }

@endphp
<div class="favourite-icon {{$user_add_item}}">
    <a href="javascript:void(0)" class="click_to_favorite_add_remove"
       data-listing_id="{{$favorite}}">
        <i class="lar la-heart icon {{$iconClass}}"></i> <span>{{ __('Favourite') }}</span>
    </a>
</div>
