@extends('layouts.app')

@section('header-title', __('Banner Placement'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Banner Placement') }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.bannerAdRequests.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">#{{ $ad->id }} — {{ $ad->title }}</h5>
                <div class="text-muted mb-3">{{ __('Type') }}: {{ $ad->type }} | {{ __('Size') }}: {{ $ad->size }}</div>

                <form action="{{ route('admin.bannerAdRequests.update', $ad->id) }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Place this banner at') }}</label>
                        <select class="form-control" name="slot_key" id="slotKeySelect">
                            @foreach($slotOptions as $key => $label)
                                <option value="{{ $key }}" {{ old('slot_key', $currentSlotKey) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('slot_key')<small class="text-danger">{{ $message }}</small>@enderror
                        <small class="text-muted">{{ __('Admin can change placement anytime. Banner must be approved to show.') }}</small>
                    </div>

                    {{-- Listing-specific scoping: only shown when a listing_details slot is selected --}}
                    <div class="col-md-6" id="listingIdRow" style="display:none">
                        <label class="form-label">{{ __('Specific Listing ID') }} <small class="text-muted">({{ __('leave blank to show on all listings') }})</small></label>
                        <input type="number" class="form-control" name="listing_id"
                               value="{{ old('listing_id', $currentListingId ?? '') }}"
                               placeholder="e.g. 42">
                        @error('listing_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <script>
                    (function(){
                        var sel = document.getElementById('slotKeySelect');
                        var row = document.getElementById('listingIdRow');
                        function toggle(){
                            row.style.display = sel.value.startsWith('listing_details') ? '' : 'none';
                        }
                        sel.addEventListener('change', toggle);
                        toggle();
                    }());
                    </script>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Redirect URL') }}</label>
                        <div>
                            <a href="{{ $ad->redirect_url }}" target="_blank" rel="noopener">{{ $ad->redirect_url }}</a>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">{{ __('Save placement') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
