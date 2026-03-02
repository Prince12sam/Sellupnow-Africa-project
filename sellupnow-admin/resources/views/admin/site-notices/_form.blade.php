{{-- Shared fields for create/edit notice --}}
<div class="row">
    <div class="col-12 mb-4">
        <label class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $notice->title ?? '') }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mb-4">
        <label class="form-label fw-semibold">{{ __('Description') }}</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $notice->description ?? '') }}</textarea>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label fw-semibold">{{ __('Notice Type') }} <span class="text-danger">*</span></label>
        <select name="notice_type" class="form-select @error('notice_type') is-invalid @enderror" required>
            @foreach(['info', 'warning', 'success', 'danger'] as $type)
                <option value="{{ $type }}" {{ old('notice_type', $notice->notice_type ?? '') === $type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
        @error('notice_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label fw-semibold">{{ __('Notice For') }} <span class="text-danger">*</span></label>
        <select name="notice_for" class="form-select @error('notice_for') is-invalid @enderror" required>
            <option value="all"  {{ old('notice_for', $notice->notice_for ?? '') === 'all'   ? 'selected' : '' }}>{{ __('All Users') }}</option>
            <option value="user" {{ old('notice_for', $notice->notice_for ?? '') === 'user'  ? 'selected' : '' }}>{{ __('Registered Users') }}</option>
            <option value="guest" {{ old('notice_for', $notice->notice_for ?? '') === 'guest' ? 'selected' : '' }}>{{ __('Guests') }}</option>
        </select>
        @error('notice_for')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label fw-semibold">{{ __('Expiry Date') }} <span class="text-danger">*</span></label>
        <input type="date" name="expire_date" class="form-control @error('expire_date') is-invalid @enderror"
            value="{{ old('expire_date', isset($notice) ? \Illuminate\Support\Carbon::parse($notice->expire_date)->format('Y-m-d') : '') }}" required>
        @error('expire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label fw-semibold d-block">{{ __('Status') }}</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="status" value="1"
                {{ old('status', ($notice->status ?? 1)) ? 'checked' : '' }}>
            <label class="form-check-label">{{ __('Active') }}</label>
        </div>
    </div>
</div>
