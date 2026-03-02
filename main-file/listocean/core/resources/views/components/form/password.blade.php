<div class="form__input__single mb-3">
    <label class="label-title">{{ $title ?? '' }}</label>
    <div class="form__input__single position-relative">
        <input type="{{ $type ?? '' }}" name="{{ $name ?? '' }}" id="{{ $id ?? '' }}" class="{{ $class ?? 'form__control' }}" placeholder="{{ $placeholder ?? '' }}">
        <div class="icon toggle-password position-absolute">
            <i class="las la-eye"></i>
        </div>
    </div>
</div>
