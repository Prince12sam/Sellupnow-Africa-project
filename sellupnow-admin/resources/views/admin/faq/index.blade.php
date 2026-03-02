@extends('layouts.app')
@section('header-title', __('FAQ Management'))
@section('content')
<div class="container-fluid my-4">

    {{-- Header --}}
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-1 mb-3">
        <div>
            <h4 class="m-0">{{ __('Frequently Asked Questions') }}</h4>
            <p class="text-muted small m-0">{{ __('Manage Q&A items shown in the app FAQ screen') }}</p>
        </div>
        @hasPermission('admin.faq.create')
            <a href="{{ route('admin.faq.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>{{ __('Add New FAQ') }}
            </a>
        @endhasPermission
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table border-left-right mb-0" id="faqTable">
                    <thead>
                        <tr>
                            <th style="width:40px" class="text-center text-muted" title="{{ __('Drag to reorder') }}">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </th>
                            <th style="width:50px" class="text-center">{{ __('Order') }}</th>
                            <th>{{ __('Question') }}</th>
                            <th style="width:220px">{{ __('Answer Preview') }}</th>
                            <th class="text-center" style="width:100px">{{ __('Status') }}</th>
                            <th class="text-center" style="width:110px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="sortableBody">
                        @forelse($faqs as $faq)
                            <tr data-id="{{ $faq->id }}" style="cursor:grab">
                                <td class="text-center text-muted drag-handle" style="cursor:grab">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </td>
                                <td class="text-center text-muted small">{{ $faq->sort_order }}</td>
                                <td class="fw-semibold" style="max-width:340px">
                                    {{ $faq->question }}
                                </td>
                                <td class="text-muted small" style="max-width:220px">
                                    {{ Str::limit(strip_tags($faq->answer), 90) }}
                                </td>
                                <td class="text-center">
                                    @hasPermission('admin.faq.toggle')
                                        <form method="POST" action="{{ route('admin.faq.toggle', $faq) }}" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $faq->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                title="{{ $faq->is_active ? __('Active – click to deactivate') : __('Inactive – click to activate') }}">
                                                {{ $faq->is_active ? __('Active') : __('Inactive') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge {{ $faq->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $faq->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    @endhasPermission
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @hasPermission('admin.faq.edit')
                                            <a href="{{ route('admin.faq.edit', $faq) }}"
                                               class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        @endhasPermission
                                        @hasPermission('admin.faq.destroy')
                                            <form method="POST" action="{{ route('admin.faq.destroy', $faq) }}"
                                                  onsubmit="return confirm('{{ __('Delete this FAQ?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endhasPermission
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-circle-question fa-2x mb-2 d-block"></i>
                                    {{ __('No FAQs yet.') }}
                                    <a href="{{ route('admin.faq.create') }}" class="ms-1">{{ __('Add the first one') }}</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-3 mb-0 small">
        <i class="fa-solid fa-circle-info me-1"></i>
        {{ __('Drag rows to reorder. Changes are saved automatically. Active FAQs appear in the mobile app FAQ screen.') }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
(function () {
    var tbody = document.getElementById('sortableBody');
    if (!tbody) return;

    Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function () {
            var ids = Array.from(tbody.querySelectorAll('tr[data-id]'))
                          .map(function (tr) { return parseInt(tr.getAttribute('data-id'), 10); });

            fetch('{{ route('admin.faq.sort') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: ids })
            }).then(function (r) {
                if (!r.ok) console.warn('Sort save failed');
                // Update displayed order numbers
                tbody.querySelectorAll('tr[data-id]').forEach(function (tr, i) {
                    var orderCell = tr.querySelector('td:nth-child(2)');
                    if (orderCell) orderCell.textContent = i + 1;
                });
            });
        }
    });
}());
</script>
@endpush
