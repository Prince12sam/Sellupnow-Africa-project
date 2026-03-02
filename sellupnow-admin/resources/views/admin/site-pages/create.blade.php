@extends('layouts.app')
@section('header-title', __('New Page'))
@section('content')
<div class="container-fluid my-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 px-1">
        <h4 class="m-0">{{ __('New Website Page') }}</h4>
        <a href="{{ route('admin.sitePages.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back to Pages') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.sitePages.store') }}">
        @csrf
        <div class="row">
            <div class="col-xl-9">
                <div class="card mb-3">
                    <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Page Content') }}</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="titleInput"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('HTML Content') }}</label>
                            <div id="editor" style="min-height:320px">{!! old('page_content') !!}</div>
                            <input type="hidden" name="page_content" id="page_content" value="{{ old('page_content') }}">
                            @error('page_content')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card mb-3">
                    <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Publish') }}</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="publish" {{ old('status', 'publish') === 'publish' ? 'selected' : '' }}>{{ __('Published') }}</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('URL Slug') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-muted">/</span>
                                <input type="text" name="slug" id="slugInput"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug') }}" placeholder="auto-generated">
                            </div>
                            @error('slug')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                            <div class="form-text">{{ __('Leave blank to auto-generate from title.') }}</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-plus me-1"></i>{{ __('Create Page') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'font': [] }],
                ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'align': [] }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    quill.on('text-change', function () {
        document.getElementById('page_content').value = quill.root.innerHTML;
    });

    // Auto-generate slug from title
    document.getElementById('titleInput')?.addEventListener('input', function () {
        const slugInput = document.getElementById('slugInput');
        if (!slugInput.dataset.manual) {
            slugInput.value = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .trim()
                .replace(/[\s_]+/g, '-');
        }
    });
    document.getElementById('slugInput')?.addEventListener('input', function () {
        this.dataset.manual = '1';
    });
</script>
@endpush
@endsection
