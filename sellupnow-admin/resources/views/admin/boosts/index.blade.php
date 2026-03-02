@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Boosts</h3>
        <a href="{{ route('admin.boosts.create') }}" class="btn btn-primary float-end">Create</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Listing</th>
                    <th>Type</th>
                    <th>Priority</th>
                    <th>Starts</th>
                    <th>Ends</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $it)
                <tr>
                    <td>{{ $it->id }}</td>
                    <td>
                        @if($it->relationLoaded('listing') && $it->listing)
                            <a href="{{ route('admin.listingModeration.index') }}?search={{ urlencode($it->listing->title) }}">{{ $it->listing->title }} <small class="text-muted">(#{{ $it->listing->id }})</small></a>
                        @else
                            #{{ $it->listing_id }}
                        @endif
                    </td>
                    <td>{{ $it->type }}</td>
                    <td>{{ $it->priority }}</td>
                    <td>{{ optional($it->starts_at)->toDateTimeString() ?? '-' }}</td>
                    <td>{{ optional($it->ends_at)->toDateTimeString() ?? '-' }}</td>
                    <td>
                        @php
                            $now = \Illuminate\Support\Carbon::now();
                            $status = 'Active';
                            if ($it->starts_at && $it->starts_at->isFuture()) {
                                $status = 'Upcoming';
                            } elseif ($it->ends_at && $it->ends_at->isPast()) {
                                $status = 'Expired';
                            }
                        @endphp
                        <span class="badge {{ $status === 'Active' ? 'bg-success' : ($status === 'Upcoming' ? 'bg-info' : 'bg-secondary') }}">{{ $status }}</span>
                        <div class="mt-2">
                            <a href="{{ route('admin.boosts.edit', $it->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                            <a href="{{ route('admin.boosts.destroy', $it->id) }}" class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $items->links() }}
    </div>
</div>
@endsection
