<table class="table_activation">
    <thead>
    <tr>
        <th>{{__('ID')}}</th>
        <th>{{__('Name')}}</th>
        <th>{{__('Email')}}</th>
        <th>{{__('Phone')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @if($all_users->total() >=1)
        @foreach($all_users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->first_name.' '.$user->last_name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                @can('user-permanent-delete')
                    <td>
                        <x-popup.delete :title="__('Delete Permanently')" :url="route('admin.user.permanent.delete',$user->id)"/>
                    </td>
                @endcan
            </tr>
        @endforeach
    @else
        <x-table.no-data-found :colspan="'5'" :class="'text-danger text-center py-5'" />
    @endif
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_users"/>
