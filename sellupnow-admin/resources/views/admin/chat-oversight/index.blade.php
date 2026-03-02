@extends('layouts.app')
@section('header-title', __('In-App Chat Oversight'))

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('In-App Chat Oversight') }}</h4>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.chatOversight.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ $search }}" class="form-control"
                        placeholder="{{ __('Search by customer/shop/phone/email') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table border-left-right">
                    <thead>
                        <tr>
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Shop') }}</th>
                            <th>{{ __('Last Message') }}</th>
                            <th>{{ __('Unread') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($threads as $index => $thread)
                            <tr>
                                <td>{{ $threads->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-medium">{{ $thread->user?->name ?? __('N/A') }}</div>
                                    <small class="text-muted">{{ $thread->user?->phone ?? $thread->user?->email ?? __('N/A') }}</small>
                                </td>
                                <td>{{ $thread->shop?->name ?? __('N/A') }}</td>
                                <td>
                                    <small>
                                        {{ \Illuminate\Support\Str::limit($thread->latestMessage?->message ?? __('No messages yet'), 60) }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge {{ $thread->unread_messages_count > 0 ? 'bg-danger' : 'bg-secondary' }}">
                                        {{ $thread->unread_messages_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.chatOversight.show', $thread->id) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('No conversations found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $threads->links() }}
            </div>
        </div>
    </div>
@endsection
