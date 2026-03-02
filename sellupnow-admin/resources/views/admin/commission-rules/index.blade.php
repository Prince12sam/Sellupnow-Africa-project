@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Commission Rules</h3>
        <a href="{{ route('admin.commissionRules.create') }}" class="btn btn-primary float-end">Create</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Scope</th>
                    <th>Percentage</th>
                    <th>Fixed</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($rules as $rule)
                <tr>
                    <td>{{ $rule->id }}</td>
                    <td>{{ $rule->name }}</td>
                    <td>{{ $rule->scope }} {{ $rule->scope_id ? '('.$rule->scope_id.')' : '' }}</td>
                    <td>{{ $rule->percentage }}</td>
                    <td>{{ $rule->fixed }}</td>
                    <td>{{ $rule->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('admin.commissionRules.edit', $rule->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <a href="{{ route('admin.commissionRules.destroy', $rule->id) }}" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $rules->links() }}
    </div>
</div>
@endsection
