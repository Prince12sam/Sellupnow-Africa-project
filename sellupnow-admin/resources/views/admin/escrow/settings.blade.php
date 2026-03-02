@extends('layouts.app')

@section('header-title', __('Escrow Settings'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Escrow Settings') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.escrow.settings.update') }}">
                    @csrf

                    @php
                        $selectedIncluded = old('included_category_ids', $options['included_category_ids'] ?? []);
                        $selectedExcluded = old('excluded_category_ids', $options['excluded_category_ids'] ?? []);

                        // Normalize to array of strings for reliable in_array checks in the view
                        if (!is_array($selectedIncluded)) $selectedIncluded = explode(',', (string) $selectedIncluded);
                        $selectedIncluded = array_map('strval', array_filter($selectedIncluded, fn($v) => $v !== null && $v !== ''));

                        if (!is_array($selectedExcluded)) $selectedExcluded = explode(',', (string) $selectedExcluded);
                        $selectedExcluded = array_map('strval', array_filter($selectedExcluded, fn($v) => $v !== null && $v !== ''));
                    @endphp

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="enabled" value="1" @checked(old('enabled', $options['enabled'] ?? false))>
                                <label class="form-check-label">{{ __('Enable Escrow (Buyer Protection)') }}</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Escrow Fee Percent') }}</label>
                            <input type="number" step="0.01" class="form-control" name="fee_percent" value="{{ old('fee_percent', $options['fee_percent'] ?? '2.5') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Escrow Currency') }}</label>
                            <input type="text" class="form-control" name="currency" value="{{ old('currency', $options['currency'] ?? 'GHS') }}">
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label">{{ __('Escrow Minimum Price') }}</label>
                            <input type="number" step="0.01" class="form-control" name="min_price" value="{{ old('min_price', $options['min_price'] ?? '0') }}">
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label">{{ __('Escrow Maximum Price') }}</label>
                            <input type="number" step="0.01" class="form-control" name="max_price" value="{{ old('max_price', $options['max_price'] ?? '999999999') }}">
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label">{{ __('Seller Accept Hours') }}</label>
                            <input type="number" class="form-control" name="seller_accept_hours" value="{{ old('seller_accept_hours', $options['seller_accept_hours'] ?? '24') }}">
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label">{{ __('Buyer Confirm Hours') }}</label>
                            <input type="number" class="form-control" name="buyer_confirm_hours" value="{{ old('buyer_confirm_hours', $options['buyer_confirm_hours'] ?? '72') }}">
                        </div>

                        <div class="col-12 mt-3">
                            <hr>
                            <p class="text-muted small">{{ __('Optional: include or exclude categories. Select items below or enter IDs as comma-separated values.') }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Included Categories') }}</label>
                            @if(!empty($categories) && count($categories) > 0)
                                <select name="included_category_ids[]" class="form-control" multiple size="8">
                                    @foreach($categories as $cat)
                                        @php $catId = (string) ($cat->id ?? $cat['id'] ?? ''); @endphp
                                        <option value="{{ $catId }}" @if(in_array($catId, $selectedIncluded)) selected @endif>
                                            {{ $cat->name ?? ($cat['name'] ?? '') }} (#{{ $catId }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="alert alert-secondary small">{{ __('No categories found. You may enter category IDs manually below.') }}</div>
                            @endif
                            <small class="text-muted">{{ __('Or enter comma-separated IDs: 1,2,3') }}</small>
                            <input type="text" name="included_category_ids_csv" value="{{ old('included_category_ids_csv', implode(',', $options['included_category_ids'] ?? [])) }}" class="form-control mt-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Excluded Categories') }}</label>
                            @if(!empty($categories) && count($categories) > 0)
                                <select name="excluded_category_ids[]" class="form-control" multiple size="8">
                                    @foreach($categories as $cat)
                                        @php $catId = (string) ($cat->id ?? $cat['id'] ?? ''); @endphp
                                        <option value="{{ $catId }}" @if(in_array($catId, $selectedExcluded)) selected @endif>
                                            {{ $cat->name ?? ($cat['name'] ?? '') }} (#{{ $catId }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="alert alert-secondary small">{{ __('No categories found. You may enter category IDs manually below.') }}</div>
                            @endif
                            <small class="text-muted">{{ __('Or enter comma-separated IDs: 1,2,3') }}</small>
                            <input type="text" name="excluded_category_ids_csv" value="{{ old('excluded_category_ids_csv', implode(',', $options['excluded_category_ids'] ?? [])) }}" class="form-control mt-1">
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('Update Escrow Settings') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
