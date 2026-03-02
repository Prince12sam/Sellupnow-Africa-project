<div class="form__input__single {{ $divClass ?? 'mb-3' }}">
    <label for="{{ $id ?? '' }}" class="{{ $labelClass ?? 'form__input__single__label'}}">{{ $title ?? '' }} @if(!empty($required)) <span class="text-danger">*</span> @endif </label>
    <input type="{{ $type ?? '' }}" name="{{ $name ?? '' }}" id="{{ $id ?? '' }}" value="{{ $value ?? '' }}" step="{{ $step ?? '' }}" placeholder="{{ $placeholder ?? '' }}" class="{{ $class ?? 'form-control' }}" >
</div>
