@php $pkg = $pkg ?? null; @endphp

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">{{ __('Package Name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $pkg->name ?? '') }}" required maxlength="191"
               placeholder="{{ __('e.g. Bronze — 7 days') }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">{{ __('Description') }}</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                  rows="3" placeholder="{{ __('Briefly describe this package') }}">{{ old('description', $pkg->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ __('Price') }} <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="price"
               class="form-control @error('price') is-invalid @enderror"
               value="{{ old('price', $pkg->price ?? '') }}" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ __('Currency') }}</label>
        <input type="text" name="currency" maxlength="3"
               class="form-control @error('currency') is-invalid @enderror"
               value="{{ old('currency', $pkg->currency ?? 'GHS') }}"
               placeholder="GHS">
        @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ __('Duration (days)') }} <span class="text-danger">*</span></label>

        {{-- Quick-select preset buttons --}}
        <div class="mb-2 d-flex flex-wrap gap-1" id="duration-presets">
            @foreach([2, 3, 7, 14, 30, 60, 90] as $preset)
                <button type="button"
                        class="btn btn-sm btn-outline-secondary duration-preset-btn"
                        data-days="{{ $preset }}">
                    {{ $preset }}d
                </button>
            @endforeach
        </div>

        <input type="number" min="1" max="3650" name="duration_days"
               id="duration_days"
               class="form-control @error('duration_days') is-invalid @enderror"
               value="{{ old('duration_days', $pkg->duration_days ?? '') }}" required>
        <div class="form-text">{{ __('How many days the featured promotion lasts. Min 1 — Max 3,650.') }}</div>
        @error('duration_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @once
    @push('scripts')
    <script>
    document.querySelectorAll('.duration-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('duration_days').value = this.dataset.days;
            document.querySelectorAll('.duration-preset-btn').forEach(function (b) {
                b.classList.remove('btn-secondary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-secondary');
        });
    });
    // Highlight the preset that matches the current value on page load
    (function () {
        var current = document.getElementById('duration_days').value;
        if (!current) return;
        document.querySelectorAll('.duration-preset-btn').forEach(function (btn) {
            if (btn.dataset.days === current) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-secondary');
            }
        });
    })();
    </script>
    @endpush
    @endonce

    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ __('Ad Listing Limit') }} <span class="text-danger">*</span></label>
        <input type="number" min="1" max="100000" name="advertisement_limit"
               class="form-control @error('advertisement_limit') is-invalid @enderror"
               value="{{ old('advertisement_limit', $pkg->advertisement_limit ?? '') }}" required>
        <div class="form-text">{{ __('Max number of listings the user can feature under this package.') }}</div>
        @error('advertisement_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                   @checked(old('is_active', $pkg->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('Active (visible to users)') }}</label>
        </div>
    </div>
</div>
