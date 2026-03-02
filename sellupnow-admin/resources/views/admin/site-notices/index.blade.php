@extends('layouts.app')

@section('header-title', __('Notices'))

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-1 mb-3">
        <h4 class="m-0">{{ __('Notices') }}</h4>
        @hasPermission('admin.siteNotice.create')
            <a href="{{ route('admin.siteNotice.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>{{ __('Add Notice') }}
            </a>
        @endhasPermission
    </div>

    <div class="card">
        <div class="card-header py-3">
            <form method="GET" action="{{ route('admin.siteNotice.index') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('Search by title…') }}" style="max-width:300px;">
                <button type="submit" class="btn btn-outline-secondary">{{ __('Search') }}</button>
                @if(request('search'))<a href="{{ route('admin.siteNotice.index') }}" class="btn btn-outline-danger">{{ __('Clear') }}</a>@endif
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
                            <th>{{ __('For') }}</th>
                            <th>{{ __('Expires') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notices as $notice)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + ($notices->currentPage() - 1) * $notices->perPage() }}</td>
                            <td>{{ $notice->title }}</td>
                            <td>
                                <span class="badge bg-{{ $notice->notice_type === 'danger' ? 'danger' : ($notice->notice_type === 'warning' ? 'warning text-dark' : ($notice->notice_type === 'success' ? 'success' : 'info')) }}">
                                    {{ ucfirst($notice->notice_type) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($notice->notice_for) }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($notice->expire_date)->format('d M Y') }}</td>
                            <td class="text-center">
                                @hasPermission('admin.siteNotice.toggle')
                                    <a href="{{ route('admin.siteNotice.toggle', $notice->id) }}"
                                        class="badge {{ $notice->status ? 'bg-success' : 'bg-danger' }} text-decoration-none">
                                        {{ $notice->status ? __('Active') : __('Inactive') }}
                                    </a>
                                @else
                                    <span class="badge {{ $notice->status ? 'bg-success' : 'bg-danger' }}">{{ $notice->status ? __('Active') : __('Inactive') }}</span>
                                @endhasPermission
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @hasPermission('admin.siteNotice.edit')
                                        <a href="{{ route('admin.siteNotice.edit', $notice->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endhasPermission
                                    @hasPermission('admin.siteNotice.destroy')
                                        <form method="POST" action="{{ route('admin.siteNotice.destroy', $notice->id) }}"
                                            onsubmit="return confirm('{{ __('Delete this notice?') }}')">
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
                            <td colspan="7" class="text-center text-muted py-4">{{ __('No notices found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($notices->hasPages())
        <div class="card-footer">
            {{ $notices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
