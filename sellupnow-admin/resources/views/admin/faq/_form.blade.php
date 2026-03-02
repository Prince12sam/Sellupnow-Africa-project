{{--
    Shared form fields for FAQ create & edit.
    Variables: $faq (Faq|null), $nextOrder (int)
--}}

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

{{-- Question --}}
<div class="mb-4">
    <label class="form-label fw-semibold">
        {{ __('Question') }} <span class="text-danger">*</span>
    </label>
    <input type="text"
           name="question"
           class="form-control @error('question') is-invalid @enderror"
           value="{{ old('question', $faq?->question) }}"
           placeholder="{{ __('e.g. How do I post an ad?') }}"
           required>
    @error('question')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Answer --}}
<div class="mb-4">
    <label class="form-label fw-semibold">
        {{ __('Answer') }} <span class="text-danger">*</span>
    </label>
    <textarea name="answer"
              id="faqAnswer"
              class="form-control @error('answer') is-invalid @enderror"
              rows="5"
              placeholder="{{ __('Provide a clear, concise answer…') }}"
              required>{{ old('answer', $faq?->answer) }}</textarea>
    <div class="form-text">{{ __('Plain text or basic HTML is supported.') }}</div>
    @error('answer')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Sort Order & Status --}}
<div class="row g-3">
    <div class="col-sm-4">
        <label class="form-label fw-semibold">{{ __('Sort Order') }}</label>
        <input type="number"
               name="sort_order"
               class="form-control @error('sort_order') is-invalid @enderror"
               value="{{ old('sort_order', $nextOrder) }}"
               min="0">
        <div class="form-text">{{ __('Lower number = shown first.') }}</div>
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-sm-8 d-flex align-items-center pt-3">
        <div class="form-check form-switch mt-2">
            <input class="form-check-input"
                   type="checkbox"
                   name="is_active"
                   id="isActive"
                   value="1"
                   {{ old('is_active', $faq ? ($faq->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="isActive">
                {{ __('Active (visible in app)') }}
            </label>
        </div>
    </div>
</div>
