@extends('layouts.app')
@section('header-title', __('Chat Conversation'))

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-0">{{ __('Chat Conversation') }}</h4>
            <small class="text-muted">
                {{ __('Customer') }}: {{ $shopUser->user?->name ?? __('N/A') }}
                | {{ __('Shop') }}: {{ $shopUser->shop?->name ?? __('N/A') }}
            </small>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.chatOversight.markSeen', $shopUser->id) }}">
                @csrf
                <button type="submit" class="btn btn-outline-success">{{ __('Mark All Seen') }}</button>
            </form>
            <a href="{{ route('admin.chatOversight.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column gap-2">
                @forelse($messages as $message)
                    @php
                        $isCustomer = $message->type === 'user';
                    @endphp
                    <div class="border rounded p-3 {{ $isCustomer ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>
                                {{ $isCustomer ? ($shopUser->user?->name ?? __('Customer')) : ($shopUser->shop?->name ?? __('Shop')) }}
                            </strong>
                            <small class="text-muted">{{ $message->created_at?->format('M d, Y h:i A') }}</small>
                        </div>
                        <div>{{ $message->message }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">{{ __('No messages found') }}</p>
                @endforelse
            </div>

            <div class="mt-3">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
@endsection
