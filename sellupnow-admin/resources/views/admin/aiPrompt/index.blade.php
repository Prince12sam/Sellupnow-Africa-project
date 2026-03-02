@extends('layouts.app')

@section('title', __('Admin Settings'))

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center ms-2">
           <i class="bi bi-gear"></i>
            {{ __('Ai Prompt') }}
        </div>
    </div>

    <div class="row mb-5 backImg">
        @if(isset($aiListingAssistant))
            <div class="col-12">
                <div class="card mt-3 cardBox">
                    <div class="card-header d-flex align-items-center gap-2 py-3">
                        <i class="bi bi-robot"></i>
                        <h5 class="mb-0">{{ __('Listing Assistant (ListOcean)') }}</h5>
                    </div>

                    <form action="{{ route('admin.aiPrompt.listingAssistant.update') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Enabled') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="ai_listing_assistant_enabled" name="enabled" value="1" {{ !empty($aiListingAssistant['enabled']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ai_listing_assistant_enabled">{{ __('Allow users to generate title/description suggestions') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="ai_listing_assistant_model">{{ __('Model') }}</label>
                                    <input type="text" class="form-control" id="ai_listing_assistant_model" name="model" value="{{ $aiListingAssistant['model'] ?? 'gpt-4o-mini' }}" placeholder="gpt-4o-mini">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="ai_listing_assistant_daily_limit">{{ __('Daily limit per user') }}</label>
                                    <input type="number" class="form-control" id="ai_listing_assistant_daily_limit" name="daily_limit" min="1" max="500" value="{{ $aiListingAssistant['daily_limit'] ?? 20 }}">
                                </div>
                            </div>

                            @error('model')
                                <p class="text text-danger m-0 mt-2">{{ $message }}</p>
                            @enderror
                            @error('daily_limit')
                                <p class="text text-danger m-0 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        @hasPermission('admin.aiPrompt.update')
                            <div class="card-footer py-3">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary py-2 px-3">{{ __('Save And Update') }}</button>
                                </div>
                            </div>
                        @endhasPermission
                    </form>
                </div>
            </div>

            <div class="col-12">
                <div class="card mt-3 cardBox">
                    <div class="card-header d-flex align-items-center gap-2 py-3">
                        <i class="bi bi-clock-history"></i>
                        <h5 class="mb-0">{{ __('Listing Assistant Logs (Latest 50)') }}</h5>
                    </div>
                    <div class="card-body">
                        @if(!empty($aiListingAssistantLogs) && count($aiListingAssistantLogs) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle">
                                    <thead>
                                    <tr>
                                        <th>{{ __('Time') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Model') }}</th>
                                        <th>{{ __('Latency') }}</th>
                                        <th>{{ __('Input Title') }}</th>
                                        <th>{{ __('Output Title') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($aiListingAssistantLogs as $row)
                                        <tr>
                                            <td>{{ $row->created_at ?? '' }}</td>
                                            <td>{{ $row->status ?? '' }}</td>
                                            <td>{{ $row->user_id ?? '' }}</td>
                                            <td>{{ $row->model ?? '' }}</td>
                                            <td>{{ !empty($row->latency_ms) ? ($row->latency_ms . 'ms') : '' }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit((string) ($row->input_title ?? ''), 60) }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit((string) ($row->output_title ?? ''), 60) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('No logs yet.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            @if(isset($aiRecommendations))
                <div class="col-12">
                    <div class="card mt-3 cardBox">
                        <div class="card-header d-flex align-items-center gap-2 py-3">
                            <i class="bi bi-stars"></i>
                            <h5 class="mb-0">{{ __('Buyer Recommendations (AI)') }}</h5>
                        </div>

                        <form action="{{ route('admin.aiPrompt.recommendations.update') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Enabled') }}</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="ai_recommendations_enabled" name="enabled" value="1" {{ !empty($aiRecommendations['enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ai_recommendations_enabled">{{ __('Allow AI ranking for buyer recommendations') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="ai_recommendations_model">{{ __('Model') }}</label>
                                        <input type="text" class="form-control" id="ai_recommendations_model" name="model" value="{{ $aiRecommendations['model'] ?? 'gpt-4o-mini' }}" placeholder="gpt-4o-mini">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="ai_recommendations_daily_limit">{{ __('Daily limit per user') }}</label>
                                        <input type="number" class="form-control" id="ai_recommendations_daily_limit" name="daily_limit" min="1" max="500" value="{{ $aiRecommendations['daily_limit'] ?? 20 }}">
                                    </div>
                                </div>
                            </div>

                            @hasPermission('admin.aiPrompt.update')
                                <div class="card-footer py-3">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary py-2 px-3">{{ __('Save And Update') }}</button>
                                    </div>
                                </div>
                            @endhasPermission
                        </form>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card mt-3 cardBox">
                        <div class="card-header d-flex align-items-center gap-2 py-3">
                            <i class="bi bi-clock-history"></i>
                            <h5 class="mb-0">{{ __('Recommendations Logs (Latest 50)') }}</h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($aiRecommendationLogs) && count($aiRecommendationLogs) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Time') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Surface') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Model') }}</th>
                                            <th>{{ __('Candidates') }}</th>
                                            <th>{{ __('Latency') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($aiRecommendationLogs as $row)
                                            <tr>
                                                <td>{{ $row->created_at ?? '' }}</td>
                                                <td>{{ $row->status ?? '' }}</td>
                                                <td>{{ $row->surface ?? '' }}</td>
                                                <td>{{ $row->user_id ?? '' }}</td>
                                                <td>{{ $row->model ?? '' }}</td>
                                                <td>{{ $row->candidate_count ?? '' }}</td>
                                                <td>{{ !empty($row->latency_ms) ? ($row->latency_ms . 'ms') : '' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ __('No logs yet.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($aiFrontendChat))
                <div class="col-12">
                    <div class="card mt-3 cardBox">
                        <div class="card-header d-flex align-items-center gap-2 py-3">
                            <i class="bi bi-chat-dots"></i>
                            <h5 class="mb-0">{{ __('Sellupnow Agent (Frontend Popup)') }}</h5>
                        </div>

                        <form action="{{ route('admin.aiPrompt.frontendChat.update') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Enabled') }}</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="ai_frontend_chat_enabled" name="enabled" value="1" {{ !empty($aiFrontendChat['enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ai_frontend_chat_enabled">{{ __('Show the AI popup on all frontend pages') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="ai_frontend_chat_model">{{ __('Model') }}</label>
                                        <input type="text" class="form-control" id="ai_frontend_chat_model" name="model" value="{{ $aiFrontendChat['model'] ?? 'gpt-4o-mini' }}" placeholder="gpt-4o-mini">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="ai_frontend_chat_daily_limit">{{ __('Daily limit per user/IP') }}</label>
                                        <input type="number" class="form-control" id="ai_frontend_chat_daily_limit" name="daily_limit" min="1" max="500" value="{{ $aiFrontendChat['daily_limit'] ?? 40 }}">
                                    </div>
                                </div>

                                @error('model')
                                    <p class="text text-danger m-0 mt-2">{{ $message }}</p>
                                @enderror
                                @error('daily_limit')
                                    <p class="text text-danger m-0 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            @hasPermission('admin.aiPrompt.update')
                                <div class="card-footer py-3">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary py-2 px-3">{{ __('Save And Update') }}</button>
                                    </div>
                                </div>
                            @endhasPermission
                        </form>
                    </div>
                </div>
            @endif

            <div class="col-12">
                <div class="card mt-3 cardBox">
                    <div class="card-header d-flex align-items-center gap-2 py-3">
                        <i class="bi bi-shield-lock"></i>
                        <h5 class="mb-0">{{ __('Escrow / Transaction Protection') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted">{{ __('Escrow settings have been moved to a dedicated page to avoid duplication. Manage all escrow configuration there.') }}</p>
                    </div>
                    <div class="card-footer py-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.escrow.settings') }}" class="btn btn-primary py-2 px-3">{{ __('Manage Escrow Settings') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($aiKnowledgeBaseDocs))
                <div class="col-12">
                    <div class="card mt-3 cardBox">
                        <div class="card-header d-flex align-items-center gap-2 py-3">
                            <i class="bi bi-journal-text"></i>
                            <h5 class="mb-0">{{ __('Sellupnow Agent Knowledge Base (PDF)') }}</h5>
                        </div>

                        <form action="{{ route('admin.aiPrompt.knowledgeBase.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <label class="form-label" for="ai_kb_pdf">{{ __('Upload PDF') }}</label>
                                        <input type="file" class="form-control" id="ai_kb_pdf" name="pdf" accept="application/pdf" required>
                                        <small class="text-muted d-block mt-1">{{ __('PDF text is extracted and used as platform knowledge for the popup.') }}</small>
                                    </div>
                                    <div class="col-md-4">
                                        @hasPermission('admin.aiPrompt.update')
                                            <button type="submit" class="btn btn-primary w-100 py-2">{{ __('Upload') }}</button>
                                        @endhasPermission
                                    </div>
                                </div>
                                @error('pdf')
                                    <p class="text text-danger m-0 mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card mt-3 cardBox">
                        <div class="card-header d-flex align-items-center gap-2 py-3">
                            <i class="bi bi-files"></i>
                            <h5 class="mb-0">{{ __('Knowledge Base Documents (Latest 20)') }}</h5>
                            <form class="ms-3" method="GET" action="{{ route('admin.aiPrompt.index') }}">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" name="kb_q" value="{{ request('kb_q') }}" placeholder="{{ __('Search filename...') }}">
                                    <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                                </div>
                            </form>
                            <div class="ms-auto">
                                @hasPermission('admin.aiPrompt.update')
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#kbClearAllModal">
                                        {{ __('Clear All') }}
                                    </button>
                                @endhasPermission
                            </div>
                        </div>
                        <div class="card-body">
                            @if(!empty($aiKnowledgeBaseDocs) && count($aiKnowledgeBaseDocs) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('File') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Active') }}</th>
                                            <th>{{ __('Uploaded') }}</th>
                                            <th class="text-end">{{ __('Action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($aiKnowledgeBaseDocs as $row)
                                            <tr>
                                                <td>{{ $row->id ?? '' }}</td>
                                                <td>{{ $row->original_filename ?? '' }}</td>
                                                <td>{{ $row->mime ?? '' }}</td>
                                                <td>
                                                    @if(!empty($row->is_active))
                                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $row->created_at ?? '' }}</td>
                                                <td class="text-end">
                                                    @hasPermission('admin.aiPrompt.update')
                                                        <a href="{{ route('admin.aiPrompt.knowledgeBase.preview', (int) $row->id) }}" class="btn btn-sm btn-outline-primary js-kb-preview" data-id="{{ (int) $row->id }}">{{ __('Preview') }}</a>
                                                        <form action="{{ route('admin.aiPrompt.knowledgeBase.toggle', (int) $row->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">{{ !empty($row->is_active) ? __('Disable') : __('Enable') }}</button>
                                                        </form>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#kbDeleteModal" data-kb-delete-action="{{ route('admin.aiPrompt.knowledgeBase.delete', (int) $row->id) }}" data-kb-delete-name="{{ (string) ($row->original_filename ?? '') }}">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    @endhasPermission
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ __('No knowledge base documents uploaded yet.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @hasPermission('admin.aiPrompt.update')
                    <div class="modal fade" id="kbClearAllModal" tabindex="-1" aria-labelledby="kbClearAllModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="kbClearAllModalLabel">{{ __('Clear Knowledge Base') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">{{ __('This will remove all uploaded knowledge base documents. Continue?') }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                    <form action="{{ route('admin.aiPrompt.knowledgeBase.clear') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">{{ __('Clear All') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endhasPermission

                <div class="modal fade" id="kbPreviewModal" tabindex="-1" aria-labelledby="kbPreviewModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="kbPreviewModalLabel">{{ __('Knowledge Base Preview') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="small text-muted mb-2" id="kbPreviewFilename"></div>
                                <pre class="mb-0" style="white-space: pre-wrap; max-height: 60vh; overflow:auto;" id="kbPreviewText">...</pre>
                            </div>
                        </div>
                    </div>
                </div>

                @hasPermission('admin.aiPrompt.update')
                    <div class="modal fade" id="kbDeleteModal" tabindex="-1" aria-labelledby="kbDeleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="kbDeleteModalLabel">{{ __('Delete Document') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">{{ __('Delete this knowledge base document?') }}</p>
                                    <div class="small text-muted mt-2" id="kbDeleteName"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                    <form id="kbDeleteForm" action="#" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endhasPermission
            @endif

            @if(isset($aiFrontendChatLogs))
                <div class="col-12">
                    <div class="card mt-3 cardBox">
                        <div class="card-header d-flex align-items-center gap-2 py-3">
                            <i class="bi bi-clock-history"></i>
                            <h5 class="mb-0">{{ __('Sellupnow Agent Chat Logs (Latest 50)') }}</h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($aiFrontendChatLogs) && count($aiFrontendChatLogs) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Time') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('IP') }}</th>
                                            <th>{{ __('Model') }}</th>
                                            <th>{{ __('KB') }}</th>
                                            <th>{{ __('Latency') }}</th>
                                            <th>{{ __('Message') }}</th>
                                            <th>{{ __('Reply') }}</th>
                                            <th>{{ __('Suggested URL') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($aiFrontendChatLogs as $row)
                                            <tr>
                                                <td>{{ $row->created_at ?? '' }}</td>
                                                <td>{{ $row->status ?? '' }}</td>
                                                <td>{{ $row->user_id ?? '' }}</td>
                                                <td>{{ $row->ip ?? '' }}</td>
                                                <td>{{ $row->model ?? '' }}</td>
                                                <td>{{ $row->kb_snippet_count ?? '' }}</td>
                                                <td>{{ !empty($row->latency_ms) ? ($row->latency_ms . 'ms') : '' }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit((string) ($row->message ?? ''), 80) }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit((string) ($row->reply ?? ''), 80) }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit((string) ($row->suggested_url ?? ''), 60) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ __('No chat logs yet.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif

@push('scripts')
    <script>
        (function () {
            function qs(sel) { return document.querySelector(sel); }
            function setText(id, value) {
                var el = qs(id);
                if (el) { el.textContent = value || ''; }
            }

            document.addEventListener('click', async function (e) {
                var a = e.target.closest('.js-kb-preview');
                if (!a) return;
                e.preventDefault();

                setText('#kbPreviewFilename', '');
                setText('#kbPreviewText', 'Loading...');

                var modalEl = document.getElementById('kbPreviewModal');
                if (modalEl && window.bootstrap) {
                    (new window.bootstrap.Modal(modalEl)).show();
                }

                try {
                    var res = await fetch(a.getAttribute('href'), { headers: { 'Accept': 'application/json' } });
                    var json = await res.json();
                    if (!res.ok) {
                        setText('#kbPreviewText', (json && json.message) ? json.message : 'Failed to load');
                        return;
                    }
                    setText('#kbPreviewFilename', (json.data && json.data.filename) ? json.data.filename : '');
                    setText('#kbPreviewText', (json.data && json.data.text) ? json.data.text : '');
                } catch (err) {
                    setText('#kbPreviewText', 'Failed to load');
                }
            });

            document.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-kb-delete-action]');
                if (!btn) return;
                var action = btn.getAttribute('data-kb-delete-action');
                var name = btn.getAttribute('data-kb-delete-name') || '';
                var form = document.getElementById('kbDeleteForm');
                if (form) {
                    form.setAttribute('action', action);
                }
                setText('#kbDeleteName', name);
            });
        })();
    </script>
@endpush

        <div class="col-md-6 col-12">
            <div class="card mt-3 cardBox">

                <div class="card-header d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-journal-bookmark-fill"></i>

                    <h5 class="mb-0">{{ __('Product Description Note') }}</h5>
                </div>

                <form action="{{ route('admin.aiPrompt.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        <div>
                            <strong>Note: </strong>Use <strong style="color: var(--theme-color) !important">
                                <i>{product_name}</i></strong> to insert the product’s name, and <strong
                                style="color: var(--theme-color) !important"><i>{short_description}</i></strong> to insert
                            the product’s short description in the prompt.

                        </div>
                        <label for="" class="mb-1 mt-3">
                            {{ __('Product Description') }} <span class="text-danger">*</span>
                        </label>
                        <textarea name="product_description" id="product_description" class="form-control" rows="4" required
                            placeholder="Enter Product Description">{{ $generaleSetting?->product_description }}</textarea>

                        @error('product_description')
                            <p class="text text-danger m-0">{{ $message }}</p>
                        @enderror

                        <input name="page_description" type="hidden" value="{{ $generaleSetting?->page_description }}">
                        <input name="blog_description" type="hidden" value="{{ $generaleSetting?->blog_description }}">

                    </div>
                    @hasPermission('admin.aiPrompt.update')
                        <div class="d-flex justify-content-end mt-4 mb-3 me-3">
                            <button type="submit" id="saveBtn1" class="btn btn-primary py-2.5 px-3">
                                {{ __('Save And Update') }}
                            </button>
                        </div>
                    @endhasPermission

                </form>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card mt-3 cardBox">
                <div class="card-header d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <h5 class="mb-0">{{ __('Page Description Note') }}</h5>
                </div>
                <form action="{{ route('admin.aiPrompt.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        <div class="mb-3">
                            <strong>Note: </strong>If you use <strong style="color: var(--theme-color) !important">
                                <i>{title}</i></strong>, the page title will be automatically retrieved and inserted into
                            the main prompt.
                        </div>

                        <input name="product_description" type="hidden"
                            value="{{ $generaleSetting?->product_description }}">
                        <input name="blog_description" type="hidden" value="{{ $generaleSetting?->blog_description }}">
                        <label for="" class="mb-1">
                            {{ __('Page  Description') }} <span class="text-danger">*</span>
                        </label>
                        <textarea name="page_description" id="page_description" class="form-control" rows="4" required
                            placeholder="Enter Page Description">{{ $generaleSetting?->page_description }}</textarea>
                        @error('page_description')
                            <p class="text text-danger m-0">{{ $message }}</p>
                        @enderror

                    </div>
                    @hasPermission('admin.aiPrompt.update')
                        <div class="d-flex justify-content-end mt-4 mb-3 me-3">
                            <button type="submit" id="saveBtn2" class="btn btn-primary py-2.5 px-3">
                                {{ __('Save And Update') }}
                            </button>
                        </div>
                    @endhasPermission

                </form>
            </div>
        </div>
        <div class="col-md-6 col-12 ">
            <div class="card mt-3 cardBox">
                <div class="card-header d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <h5 class="mb-0">{{ __('Blog Description Note') }}</h5>
                </div>
                <form action="{{ route('admin.aiPrompt.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        <div class="mb-3">
                            <strong>Note: </strong>If you use <strong style="color: var(--theme-color) !important">
                                <i>{title}</i></strong>, the blog title will be automatically retrieved and inserted into
                            the main prompt.
                        </div>

                        <input name="product_description" type="hidden"
                            value="{{ $generaleSetting?->product_description }}">
                        <input name="page_description" type="hidden" value="{{ $generaleSetting?->page_description }}">
                        <label for="" class="mb-1">
                            {{ __('Blog  Description') }} <span class="text-danger">*</span>
                        </label>
                        <textarea name="blog_description" id="blog_description" class="form-control" rows="4" required
                            placeholder="Enter Page Description">{{ $generaleSetting?->blog_description }}</textarea>

                        @error('blog_description')
                            <p class="text text-danger m-0">{{ $message }}</p>
                        @enderror

                    </div>
                    @hasPermission('admin.aiPrompt.update')
                        <div class="d-flex justify-content-end mt-4 mb-3 me-3">
                            <button type="submit" id="saveBtn3" class="btn btn-primary py-2.5 px-3">
                                {{ __('Save And Update') }}
                            </button>
                        </div>
                    @endhasPermission

                </form>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <style>
        .cardBox {
            box-shadow: 0 1px 11px rgba(99, 99, 99, 0.476) !important;
        }

        .btn-primary:disabled {
            background-color: #484848 !important;
            border-color: #484848 !important;
        }

        .backImg {
            position: relative;
            z-index: 1;
        }

        .backImg::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url("{{ asset('assets/icons-admin/intelligence.svg') }}") no-repeat center;
            background-size: contain;
            opacity: 0.06;
            z-index: -1;
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const productDesc = document.getElementById("product_description");
            const pageDesc = document.getElementById("page_description");
            const blogDesc = document.getElementById("blog_description");
            const saveBtn1 = document.getElementById("saveBtn1");
            const saveBtn2 = document.getElementById("saveBtn2");
            const saveBtn3 = document.getElementById("saveBtn3");

            function validateProductDesc() {
                const value = productDesc.value;
                const hasProductName = value.includes("{product_name}");
                const hasShortDesc = value.includes("{short_description}");
                saveBtn1.disabled = !(hasProductName && hasShortDesc);
            }

            function validatePageDesc() {
                const value = pageDesc.value;
                const hasTitle = value.includes("{title}");
                saveBtn2.disabled = !hasTitle;
            }

            function validateblogDesc() {
                const value = blogDesc.value;
                const hasTitle = value.includes("{title}");
                saveBtn3.disabled = !hasTitle;
            }

            validateProductDesc();
            validatePageDesc();
            validateblogDesc();

            productDesc.addEventListener("input", validateProductDesc);
            pageDesc.addEventListener("input", validatePageDesc);
            blogDesc.addEventListener("input", validateblogDesc);
        });
    </script>
@endpush

@push('scripts')
    <script>
        (function ($) {
            "use strict";

            $(function () {
                const $excluded = $('#escrow_excluded_category_ids');
                if ($excluded.length && typeof $excluded.select2 === 'function') {
                    $excluded.select2({
                        width: '100%',
                        placeholder: 'Select excluded categories',
                        allowClear: true,
                        closeOnSelect: false,
                    });
                }

                const $included = $('#escrow_included_category_ids');
                if ($included.length && typeof $included.select2 === 'function') {
                    $included.select2({
                        width: '100%',
                        placeholder: 'Select included categories',
                        allowClear: true,
                        closeOnSelect: false,
                    });
                }
            });
        })(jQuery);
    </script>
@endpush
