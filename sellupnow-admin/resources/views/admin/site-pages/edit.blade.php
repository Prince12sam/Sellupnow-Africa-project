@extends('layouts.app')
@section('header-title', __('Edit Page') . ' — ' . $page->title)
@section('content')
<div class="container-fluid my-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 px-1">
        <h4 class="m-0">{{ __('Edit') }}: <span class="text-primary">{{ $page->title }}</span></h4>
        <a href="{{ route('admin.sitePages.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back to Pages') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <form method="POST" action="{{ route('admin.sitePages.update', $page->id) }}">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-xl-9">
                <div class="card mb-3">
                    <div class="card-header py-3">
                        <h5 class="card-title m-0">{{ __('Page Content') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $page->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @if($page->page_builder_status === 'on')
                            <div class="alert alert-warning small mb-3">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                This page uses <strong>Page Builder blocks</strong> for its main visual layout (widgets, sections, FAQ items).
                                The HTML editor below writes to the <code>page_content</code> field which is shown <em>alongside</em> those blocks.
                                To edit FAQ items, section headings, etc., use the <strong>native page builder</strong> → Page Builder.
                            </div>
                        @endif

                        <div class="mb-1">
                            <label class="form-label fw-semibold">{{ __('HTML Content') }}</label>
                        </div>
                        <div id="editor" style="min-height:320px">{!! old('page_content', $page->page_content) !!}</div>
                        <input type="hidden" name="page_content" id="page_content"
                            value="{{ old('page_content', $page->page_content) }}">
                        @error('page_content')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
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
                                <option value="publish" {{ old('status', $page->status) === 'publish' ? 'selected' : '' }}>{{ __('Published') }}</option>
                                <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('URL Slug') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-muted">/</span>
                                <input type="text" class="form-control" value="{{ $page->slug }}" disabled>
                            </div>
                            <div class="form-text">{{ __('Slug cannot be changed after creation.') }}</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-floppy-disk me-1"></i>{{ __('Save Changes') }}
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
</script>
@endpush
@endsection
