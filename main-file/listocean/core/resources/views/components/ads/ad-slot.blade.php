@props([
    'placement'  => '',
    'class'      => '',
    'listingId'  => null,
])
@php
    use App\Models\Backend\Advertisement;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    $ad = null;
    if (!empty($placement)) {
        // Primary: look up via admin-managed frontend_ad_slots mapping
        $slotRowExists = false;
        if (Schema::hasTable('frontend_ad_slots')) {
            // Check if ANY row exists for this slot key (regardless of listing scope)
            $slotRowExists = DB::table('frontend_ad_slots')
                ->where('slot_key', $placement)
                ->where('status', 1)
                ->exists();

            $query = DB::table('frontend_ad_slots as fs')
                ->join('advertisements as a', 'a.id', '=', 'fs.advertisement_id')
                ->where('fs.slot_key', $placement)
                ->where('fs.status', 1)
                ->where('a.status', 1)
                ->where(fn ($q) => $q->whereNull('fs.start_at')->orWhere('fs.start_at', '<=', now()))
                ->where(fn ($q) => $q->whereNull('fs.end_at')->orWhere('fs.end_at', '>=', now()));

            // If listing_id column exists, scope to this listing (or global ads with no listing)
            if ($listingId !== null && Schema::hasColumn('frontend_ad_slots', 'listing_id')) {
                $query->where(fn ($q) => $q->whereNull('fs.listing_id')->orWhere('fs.listing_id', (int) $listingId));
                // Prefer listing-specific ad over global: order by listing_id DESC (NULL = global = lower priority)
                $query->orderByRaw('CASE WHEN fs.listing_id IS NULL THEN 1 ELSE 0 END ASC');
            }

            $ad = $query->select('a.*')->inRandomOrder()->first();
        }
        // Fallback: direct slot column on advertisements (legacy / admin-authored).
        // Only used when there is NO frontend_ad_slots row at all for this slot —
        // if a scoped row exists but doesn't match this listing, we must NOT fall back
        // to the global advertisements.slot lookup or the ad leaks to every listing page.
        if (!$ad && !$slotRowExists) {
            $ad = Advertisement::where('slot', $placement)
                ->where('status', 1)
                ->inRandomOrder()
                ->first();
        }
    }

    // Resolve redirect URL — fall back to the listing's own page when no URL is set
    // and we know which listing this ad is on.
    $adRedirectUrl = $ad->redirect_url ?? '';
    if (empty($adRedirectUrl) && $listingId !== null) {
        try {
            $listingSlug = DB::table('listings')->where('id', (int) $listingId)->value('slug');
            if ($listingSlug) {
                $adRedirectUrl = route('frontend.listing.details', $listingSlug);
            }
        } catch (\Throwable $e) {
            $adRedirectUrl = '';
        }
    }
    $adRedirectUrl = $adRedirectUrl ?: '#';
@endphp

@if($ad)
<div class="ad-slot ad-slot--{{ Str::slug($placement) }} {{ $class }}"
     data-ad-id="{{ $ad->id }}">
    @if(($ad->type ?? '') === 'video' && !empty($ad->image))
        {{-- Video ad — $ad->image stores the video URL --}}
        @php
            $videoSrc = $ad->image;
            if (!str_starts_with($videoSrc, 'http')) {
                $videoSrc = asset('storage/' . ltrim($videoSrc, '/'));
            }
        @endphp
        <a href="{{ $adRedirectUrl }}"
           target="{{ $adRedirectUrl !== '#' ? '_blank' : '_self' }}"
           rel="noopener sponsored"
           data-ad-click="{{ $ad->id }}"
           class="d-block text-decoration-none">
            <video src="{{ $videoSrc }}"
                   autoplay muted loop playsinline
                   style="max-width:100%;height:auto;border-radius:6px;display:block;"
                   preload="metadata">
            </video>
        </a>
    @elseif(!empty($ad->image))
        {{-- Image banner — image may be an attachment ID (int) or a URL/path string --}}
        @php
            if (is_numeric($ad->image)) {
                $imgData = get_attachment_image_by_id((int) $ad->image);
                $imgSrc  = $imgData['img_url'] ?? '';
            } elseif (str_starts_with($ad->image, 'http')) {
                $imgSrc = $ad->image;
            } else {
                $imgSrc = asset('storage/' . ltrim($ad->image, '/'));
            }
        @endphp
        @if(!empty($imgSrc))
        <a href="{{ $adRedirectUrl }}"
           target="{{ $adRedirectUrl !== '#' ? '_blank' : '_self' }}"
           rel="noopener sponsored"
           data-ad-click="{{ $ad->id }}"
           class="d-block">
            <img src="{{ $imgSrc }}"
                 alt="{{ $ad->title ?? 'Advertisement' }}"
                 style="max-width:100%;height:auto;border-radius:6px;"
                 loading="lazy">
        </a>
        @endif
    @elseif(!empty($ad->embed_code))
        {{-- Embed / Google Adsense / custom HTML --}}
        {!! $ad->embed_code !!}
    @endif
    <small class="d-block text-end text-muted" style="font-size:10px;opacity:.5;margin-top:2px;">{{ __('Ad') }}</small>
</div>
<script>
(function(){
    try {
        // Track impression
        fetch('{{ route("frontend.home.advertisement.impression.store") }}?id={{ $ad->id }}').catch(function(){});
        // Track clicks
        document.querySelectorAll('[data-ad-click="{{ $ad->id }}"]').forEach(function(el){
            el.addEventListener('click', function(){
                fetch('{{ route("frontend.home.advertisement.click.store") }}?id={{ $ad->id }}').catch(function(){});
            });
        });
    } catch(e){}
}());
</script>
@endif
