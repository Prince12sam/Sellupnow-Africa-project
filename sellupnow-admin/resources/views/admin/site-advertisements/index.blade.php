@extends('layouts.app')

@section('header-title', __('Advertisements'))

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-1 mb-3">
        <h4 class="m-0">{{ __('Advertisements') }}</h4>
        @hasPermission('admin.siteAdvertisement.create')
            <a href="{{ route('admin.siteAdvertisement.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>{{ __('Add Advertisement') }}
            </a>
        @endhasPermission
    </div>

    <div class="card mb-3">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('admin.siteAdvertisement.index') }}" class="d-flex flex-wrap gap-2">
                <input type="text" name="search" class="form-control" style="max-width:240px;" value="{{ request('search') }}" placeholder="{{ __('Search by title…') }}">
                <select name="type" class="form-select" style="max-width:160px;">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                    <option value="embed_code" {{ request('type') === 'embed_code' ? 'selected' : '' }}>{{ __('Embed Code') }}</option>
                </select>
                <select name="status" class="form-select" style="max-width:140px;">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
                <button type="submit" class="btn btn-outline-secondary">{{ __('Filter') }}</button>
                <a href="{{ route('admin.siteAdvertisement.index') }}" class="btn btn-outline-danger">{{ __('Clear') }}</a>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table border-left-right table-responsive-md mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:50px">#</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Size') }}</th>
                            <th>{{ __('Slot') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements as $ad)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + ($advertisements->currentPage() - 1) * $advertisements->perPage() }}</td>
                            <td>{{ $ad->title }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $ad->type)) }}</span></td>
                            <td>{{ $ad->size }}</td>
                            <td>{{ $ad->slot ?: '—' }}</td>
                            <td class="text-center">
                                @hasPermission('admin.siteAdvertisement.toggle')
                                    <a href="{{ route('admin.siteAdvertisement.toggle', $ad->id) }}"
                                        class="badge {{ $ad->status ? 'bg-success' : 'bg-danger' }} text-decoration-none">
                                        {{ $ad->status ? __('Active') : __('Inactive') }}
                                    </a>
                                @else
                                    <span class="badge {{ $ad->status ? 'bg-success' : 'bg-danger' }}">{{ $ad->status ? __('Active') : __('Inactive') }}</span>
                                @endhasPermission
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @hasPermission('admin.siteAdvertisement.edit')
                                        <a href="{{ route('admin.siteAdvertisement.edit', $ad->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endhasPermission
                                    @hasPermission('admin.siteAdvertisement.destroy')
                                        <form id="deleteAdvertisementForm{{ $ad->id }}" method="POST" action="{{ route('admin.siteAdvertisement.destroy', $ad->id) }}">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" onclick="confirmDeleteAdvertisement({{ $ad->id }})">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endhasPermission
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">{{ __('No advertisements found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($advertisements->hasPages())
        <div class="card-footer">{{ $advertisements->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function confirmDeleteAdvertisement(id) {
            const form = document.getElementById('deleteAdvertisementForm' + id);
            if (!form) return;

            if (typeof Swal === 'undefined' || !Swal.fire) {
                if (confirm("{{ __('Delete this advertisement? This action cannot be undone.') }}")) {
                    form.submit();
                }
                return;
            }

            Swal.fire({
                title: "{{ __('Delete this advertisement?') }}",
                text: "{{ __('This will permanently remove the ad from your site placements.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: "{{ __('Yes, delete it') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endpush
