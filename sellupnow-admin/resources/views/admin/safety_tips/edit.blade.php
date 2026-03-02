@extends('layouts.app')
@section('header-title', __('Safety Tips'))

@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-8 mx-auto">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title m-0">{{ __('Safety Tips') }}</h5>
                    <small class="text-muted">{{ __('Shown as a popup / block on the listing detail page') }}</small>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.safetyTips.update') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('Safety Tips Content') }}</label>
                        <textarea name="safety_tips_info"
                                  class="form-control @error('safety_tips_info') is-invalid @enderror"
                                  rows="8">{{ old('safety_tips_info', $text) }}</textarea>
                        <div class="form-text">
                            {{ __('HTML is supported (e.g.') }} <code>&lt;ul&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ul&gt;</code>). 
                            {{ __('Content is rendered as-is on the listing detail page.') }}
                        </div>
                        @error('safety_tips_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('Popup Background Color') }}</label>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <input type="text"
                                   name="safety_tips_color"
                                   id="safetyColorText"
                                   class="form-control @error('safety_tips_color') is-invalid @enderror"
                                   value="{{ old('safety_tips_color', $color) }}"
                                   placeholder="#fff8e1"
                                   style="max-width:260px;">
                            <input type="color"
                                   id="safetyColorPicker"
                                   value="{{ old('safety_tips_color', ($color && strlen($color) === 7 && $color[0] === '#') ? $color : '#fff8e1') }}"
                                   class="form-control form-control-color"
                                   title="{{ __('Pick a color') }}">
                            <div class="rounded border px-3 py-2 small"
                                 id="safetyColorPreview"
                                 style="background: {{ old('safety_tips_color', $color ?: '#fff8e1') }}; min-width:80px; text-align:center; color:#333">
                                {{ __('Preview') }}
                            </div>
                        </div>
                        <div class="form-text">{{ __('Hex or rgba background color for the safety tips popup/block.') }}</div>
                        @error('safety_tips_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary px-4">{{ __('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var textInput = document.getElementById('safetyColorText');
    var picker    = document.getElementById('safetyColorPicker');
    var preview   = document.getElementById('safetyColorPreview');
    if (!textInput || !picker || !preview) return;
    picker.addEventListener('input', function () {
        textInput.value       = picker.value;
        preview.style.background = picker.value;
    });
    textInput.addEventListener('input', function () {
        preview.style.background = textInput.value;
        if (/^#[0-9a-fA-F]{6}$/.test(textInput.value)) picker.value = textInput.value;
    });
}());
</script>
@endpush
