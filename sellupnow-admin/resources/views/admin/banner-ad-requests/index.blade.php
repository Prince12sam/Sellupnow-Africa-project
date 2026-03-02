@extends('layouts.app')

@section('header-title', __('Banner Ad Requests'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Banner Ad Requests') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bannerAdRequests.index') }}" class="row g-2">
                    <div class="col-md-3">
                        @php $s = request('status', $status ?? 'pending'); @endphp
                        <select class="form-control" name="status">
                            <option value="pending" {{ $s==='pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="approved" {{ $s==='approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                            <option value="all" {{ $s==='all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input class="form-control" type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search title, url, id...') }}">
                    </div>
                    <div class="col-md-3 d-grid">
                        <button class="btn btn-primary" type="submit">{{ __('Filter') }}</button>
                    </div>
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Size') }}</th>
                            <th>{{ __('Redirect') }}</th>
                            <th>{{ __('Requested placement') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($ads as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->user->name ?? '-' }}</td>
                                <td>{{ $row->title }}</td>
                                <td>{{ $row->size }}</td>
                                <td style="max-width:320px;">
                                    <a href="{{ $row->redirect_url }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit((string)$row->redirect_url, 42) }}</a>
                                </td>
                                <td>
                                    @if(!empty($row->placements))
                                        @foreach($row->placements as $p)
                                            <div class="text-muted">{{ ($p->reel_type ?? '') }} #{{ (int)($p->reel_id ?? 0) }} — {{ ($p->placement ?? '') }}</div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ (int)$row->status === 1 ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ (int)$row->status === 1 ? __('Approved') : __('Pending') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.bannerAdRequests.edit', $row->id) }}" class="btn btn-outline-secondary btn-sm">{{ __('Placement') }}</a>
                                    @if((int)$row->status === 1)
                                        <form action="{{ route('admin.bannerAdRequests.deactivate', $row->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-outline-secondary btn-sm" type="submit">{{ __('Deactivate') }}</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.bannerAdRequests.approve', $row->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-success btn-sm" type="submit">{{ __('Approve') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('No banner ad requests found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $ads->links() }}</div>
            </div>
        </div>
    </div>
@endsection
