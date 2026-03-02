@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between px-3">
        <div>
            <h4 class="mb-0">{{ __('Membership Feature Catalog') }}</h4>
        </div>
        <div>
            <a href="{{ route('admin.membershipFeature.create') }}" class="btn btn-primary">{{ __('Add Feature') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr><th>#</th><th>Key</th><th>Label</th><th>Active</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($items as $it)
                            <tr>
                                <td>{{ $it->id }}</td>
                                <td>{{ $it->key }}</td>
                                <td>{{ $it->label }}</td>
                                <td>{{ $it->is_active ? 'Yes' : 'No' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.membershipFeature.edit', $it->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <a href="{{ route('admin.membershipFeature.destroy', $it->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $items->links() }}
            </div>
        </div>
    </div>
@endsection
