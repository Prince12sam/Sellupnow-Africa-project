@extends('layouts.app')
@section('header-title', __('Edit Block') . ' — ' . $block->addon_name)
@section('content')
<div class="container-fluid my-4">

    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.sitePages.index') }}">Website Pages</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.pageBuilders.page', $page->id) }}">{{ $page->title }} — Blocks</a>
            </li>
            <li class="breadcrumb-item active">Edit: {{ $block->addon_name }}</li>
        </ol>
    </nav>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ==================== TEXT EDITOR ADDON ==================== --}}
    @if($block->addon_name === 'TextEditor')
        <form method="POST" action="{{ route('admin.pageBuilders.update', $block->id) }}">
            @csrf @method('PUT')
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-align-left text-primary"></i>
                    <strong>Text Editor Block</strong>
                    <span class="text-muted small ms-auto">Page: {{ $page->title }}</span>
                </div>
                <div class="card-body">
                    <div id="text_editor_container" style="height: 400px; border: 1px solid #dee2e6; border-radius: 4px;"></div>
                    <input type="hidden" name="text_editor" id="text_editor_input">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Save Changes</button>
            <a href="{{ route('admin.pageBuilders.page', $page->id) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>

        @push('scripts')
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            var quill = new Quill('#text_editor_container', {
                theme: 'snow',
                modules: { toolbar: [
                    [{ header: [1,2,3,false] }],
                    ['bold','italic','underline','strike'],
                    [{ color: [] },{ background: [] }],
                    [{ list: 'ordered' },{ list: 'bullet' }],
                    ['link'],['clean']
                ]}
            });
            quill.clipboard.dangerouslyPasteHTML({!! json_encode($settings['text_editor'] ?? '') !!});
            document.querySelector('form').addEventListener('submit', function() {
                document.getElementById('text_editor_input').value = quill.root.innerHTML;
            });
        </script>
        @endpush

    {{-- ==================== FAQ / FaqOne ADDON ==================== --}}
    @elseif(in_array($block->addon_name, ['FaqOne','Faq']))
        @php
            $faqKey = null;
            $faqData = [];
            foreach ($settings as $k => $v) {
                if (is_array($v) && isset($v['title_'])) {
                    $faqKey = $k;
                    $faqData = $v;
                    break;
                }
            }
            $faqTitles = $faqData['title_'] ?? [];
            $faqDescs  = $faqData['description_'] ?? [];
        @endphp
        <form method="POST" action="{{ route('admin.pageBuilders.update', $block->id) }}">
            @csrf @method('PUT')
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-question text-warning"></i>
                    <strong>FAQ Block — {{ $block->addon_name }}</strong>
                </div>
                <div class="card-body">
                    {{-- Section title / header info --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Section Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $settings['title'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Prompt Text</label>
                            <input type="text" name="contact_info" class="form-control" value="{{ old('contact_info', $settings['contact_info'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contact Button Label</label>
                            <input type="text" name="contact_info_title" class="form-control" value="{{ old('contact_info_title', $settings['contact_info_title'] ?? '') }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Contact Button Link</label>
                            <input type="text" name="contact_info_link" class="form-control" value="{{ old('contact_info_link', $settings['contact_info_link'] ?? '') }}">
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0">FAQ Items</h6>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="addFaqRow()">
                            <i class="fa-solid fa-plus me-1"></i>Add Question
                        </button>
                    </div>

                    <div id="faq-rows">
                        @foreach($faqTitles as $idx => $qtitle)
                        <div class="faq-row border rounded p-3 mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold small text-muted">Q{{ $idx + 1 }}</span>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.faq-row').remove()">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <input type="text" name="faq_titles[]" class="form-control mb-2"
                                placeholder="Question" value="{{ old('faq_titles.'.$idx, $qtitle) }}">
                            <textarea name="faq_descs[]" class="form-control" rows="3"
                                placeholder="Answer">{{ old('faq_descs.'.$idx, $faqDescs[$idx] ?? '') }}</textarea>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Save Changes</button>
            <a href="{{ route('admin.pageBuilders.page', $page->id) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>

        @push('scripts')
        <script>
        function addFaqRow() {
            var container = document.getElementById('faq-rows');
            var idx = container.querySelectorAll('.faq-row').length + 1;
            var html = `<div class="faq-row border rounded p-3 mb-2 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold small text-muted">Q${idx}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.faq-row').remove()"><i class="fa-solid fa-trash"></i></button>
                </div>
                <input type="text" name="faq_titles[]" class="form-control mb-2" placeholder="Question">
                <textarea name="faq_descs[]" class="form-control" rows="3" placeholder="Answer"></textarea>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }
        </script>
        @endpush

    {{-- ==================== CONTACT INFO ADDON ==================== --}}
    @elseif($block->addon_name === 'ContactInfo')
        @php
            $iconKey = null;
            foreach ($settings as $k => $v) {
                if (is_array($v) && isset($v['icon_'])) { $iconKey = $k; break; }
            }
            $icons = $iconKey ? ($settings[$iconKey]['icon_'] ?? []) : [];
            $links = $iconKey ? ($settings[$iconKey]['icon_link_'] ?? []) : [];
        @endphp
        <form method="POST" action="{{ route('admin.pageBuilders.update', $block->id) }}">
            @csrf @method('PUT')
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-address-card text-info"></i>
                    <strong>Contact Info Block</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Section Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $settings['title'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subtitle</label>
                            <input type="text" name="sub_title" class="form-control" value="{{ old('sub_title', $settings['sub_title'] ?? $settings['subtitle'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $settings['address'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $settings['email'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings['phone'] ?? '') }}">
                        </div>
                    </div>

                    @if($iconKey)
                    <hr>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0">Social Links</h6>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="addSocialRow()">
                            <i class="fa-solid fa-plus me-1"></i>Add Social Link
                        </button>
                    </div>
                    <p class="small text-muted">Use <a href="https://lineicons.com/icons/" target="_blank">LineIcons</a> or Font Awesome class names (e.g. <code>lab la-facebook-f</code>, <code>fab fa-twitter</code>)</p>
                    <div id="social-rows">
                        @foreach($icons as $idx => $ic)
                        <div class="social-row d-flex gap-2 align-items-center mb-2">
                            <input type="text" name="icon_class[]" class="form-control" placeholder="Icon class" value="{{ old('icon_class.'.$idx, $ic) }}">
                            <input type="text" name="icon_link[]" class="form-control" placeholder="URL" value="{{ old('icon_link.'.$idx, $links[$idx] ?? '') }}">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.social-row').remove()">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Save Changes</button>
            <a href="{{ route('admin.pageBuilders.page', $page->id) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>

        @push('scripts')
        <script>
        function addSocialRow() {
            document.getElementById('social-rows').insertAdjacentHTML('beforeend',
                `<div class="social-row d-flex gap-2 align-items-center mb-2">
                    <input type="text" name="icon_class[]" class="form-control" placeholder="Icon class e.g. lab la-facebook-f">
                    <input type="url" name="icon_link[]" class="form-control" placeholder="https://...">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.social-row').remove()"><i class="fa-solid fa-trash"></i></button>
                </div>`
            );
        }
        </script>
        @endpush

    {{-- ==================== MARKETPLACE ONE ADDON ==================== --}}
    @elseif($block->addon_name === 'MarketPlaceOne')
        <div class="row g-4">

            {{-- Image upload card --}}
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="fa-solid fa-image text-success"></i>
                        <strong>Section Image</strong>
                        <span class="badge bg-secondary ms-auto">845 × 800 px</span>
                    </div>
                    <div class="card-body d-flex flex-column gap-3">

                        {{-- Current image preview --}}
                        @if(!empty($bannerImageInfo))
                        <div class="text-center">
                            <p class="text-muted small mb-1">Current image (ID: {{ $bannerImageInfo['id'] }})</p>
                            <img src="{{ $bannerImageInfo['url'] }}"
                                 alt="Banner image"
                                 class="img-fluid rounded border"
                                 style="max-height:260px; object-fit:contain;">
                        </div>
                        @else
                        <div class="text-center text-muted py-4 border rounded bg-light">
                            <i class="fa-solid fa-image fa-3x mb-2 d-block opacity-25"></i>
                            No image set
                        </div>
                        @endif

                        {{-- Upload form --}}
                        <form method="POST"
                              action="{{ route('admin.pageBuilders.update', $block->id) }}"
                              enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <label class="form-label fw-semibold">Upload New Image</label>
                            <input type="file" name="banner_image_file" class="form-control mb-2" accept="image/*" required>
                            <p class="text-muted small">JPG/PNG/WebP · Max 4 MB · Ideal size: 845×800 px</p>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fa-solid fa-upload me-1"></i> Upload &amp; Save Image
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            {{-- Text / buttons card --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-primary"></i>
                        <strong>Text &amp; Buttons</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.pageBuilders.update', $block->id) }}">
                            @csrf @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" class="form-control"
                                       value="{{ old('title', $settings['title'] ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Subtitle</label>
                                <textarea name="subtitle" class="form-control" rows="3">{{ old('subtitle', $settings['subtitle'] ?? '') }}</textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Button 1 Label</label>
                                    <input type="text" name="button_one_title" class="form-control"
                                           value="{{ old('button_one_title', $settings['button_one_title'] ?? '') }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Button 1 Link</label>
                                    <input type="text" name="button_one_link" class="form-control"
                                           value="{{ old('button_one_link', $settings['button_one_link'] ?? '') }}">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Button 2 Label</label>
                                    <input type="text" name="button_two_title" class="form-control"
                                           value="{{ old('button_two_title', $settings['button_two_title'] ?? '') }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Button 2 Link</label>
                                    <input type="text" name="button_two_link" class="form-control"
                                           value="{{ old('button_two_link', $settings['button_two_link'] ?? '') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-1"></i> Save Text &amp; Buttons
                            </button>
                            <a href="{{ route('admin.pageBuilders.page', $page->id) }}"
                               class="btn btn-outline-secondary ms-2">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    {{-- ==================== GENERIC TITLE/SUBTITLE ADDON ==================== --}}
    @elseif(array_key_exists('title', $settings) || array_key_exists('subtitle', $settings))
        <form method="POST" action="{{ route('admin.pageBuilders.update', $block->id) }}">
            @csrf @method('PUT')
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-cube text-secondary"></i>
                    <strong>{{ $block->addon_name }}</strong> — Block Settings
                </div>
                <div class="card-body">
                    @if(array_key_exists('title', $settings))
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $settings['title'] ?? '') }}">
                    </div>
                    @endif
                    @if(array_key_exists('subtitle', $settings))
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $settings['subtitle'] ?? '') }}">
                    </div>
                    @endif
                    @if(array_key_exists('button_title_one', $settings))
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Button 1 Label</label>
                            <input type="text" name="button_title_one" class="form-control" value="{{ old('button_title_one', $settings['button_title_one'] ?? '') }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Button 1 Link</label>
                            <input type="text" name="button_link_one" class="form-control" value="{{ old('button_link_one', $settings['button_link_one'] ?? '') }}">
                        </div>
                    </div>
                    @endif
                    @if(array_key_exists('button_title_two', $settings))
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Button 2 Label</label>
                            <input type="text" name="button_title_two" class="form-control" value="{{ old('button_title_two', $settings['button_title_two'] ?? '') }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Button 2 Link</label>
                            <input type="text" name="button_link_two" class="form-control" value="{{ old('button_link_two', $settings['button_link_two'] ?? '') }}">
                        </div>
                    </div>
                    @endif

                    {{-- Notice if this addon has complex arrays --}}
                    @php $hasArrays = collect($settings)->filter(fn($v) => is_array($v))->isNotEmpty(); @endphp
                    @if($hasArrays)
                        <div class="alert alert-info small mt-3 mb-0">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            This block also has complex array data (images, team members, etc.).
                            Use the <strong>Raw JSON Editor</strong> below to edit those fields.
                        </div>
                    @endif
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Save Changes</button>
            <a href="{{ route('admin.pageBuilders.page', $page->id) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>

    @else
        {{-- Fallback: no recognized structured fields — show info --}}
        <div class="alert alert-warning">
            This block (<strong>{{ $block->addon_name }}</strong>) does not have simple text fields. Use the Raw JSON Editor below to edit it.
        </div>
    @endif

    {{-- ==================== RAW JSON EDITOR (always shown, collapsed) ==================== --}}
    <div class="mt-4">
        <div class="accordion" id="rawJsonAccordion">
            <div class="accordion-item border">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rawJsonCollapse">
                        <i class="fa-solid fa-code me-2"></i> Raw JSON Editor (Advanced)
                    </button>
                </h2>
                <div id="rawJsonCollapse" class="accordion-collapse collapse" data-bs-parent="#rawJsonAccordion">
                    <div class="accordion-body">
                        <p class="small text-muted">
                            Edit the <code>addon_settings</code> JSON directly. This overrides all structured inputs above when saved here.
                            Be careful with JSON syntax. Use a validator like <a href="https://jsonlint.com" target="_blank">jsonlint.com</a> if unsure.
                        </p>
                        <form method="POST" action="{{ route('admin.pageBuilders.update', $block->id) }}">
                            @csrf @method('PUT')
                            <textarea name="addon_settings_raw" class="form-control font-monospace" rows="24"
                                style="font-size:.82em;">{{ json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</textarea>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa-solid fa-code me-1"></i>Save Raw JSON
                                </button>
                                <a href="{{ route('admin.pageBuilders.page', $page->id) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
