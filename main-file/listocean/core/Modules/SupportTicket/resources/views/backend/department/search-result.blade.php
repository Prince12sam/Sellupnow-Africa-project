<table class="DataTable_activation">
    <thead>
    <tr>
        <th>{{__('ID')}}</th>
        <th>{{__('Department')}}</th>
        <th>{{__('Status')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($departments as $department)
        <tr>
            <td>{{ $department->id }}</td>
            <td>{{ $department->name }}</td>
            <td><x-status.table.active-inactive :status="$department->status"/></td>
            <td>
                @can('department-edit')
                    <a class="cmnBtn btn_5 btn_bg_warning radius-5 btnIcon edit_department_modal"
                       data-bs-toggle="modal"
                       data-bs-target="#editCountryModal"
                       data-department="{{$department->name}}"
                       data-department_id="{{ $department->id }}">
                        <i class="las la-pen"></i>
                    </a>
                @endcan
                @can('department-bulk-delete')
               <x-popup.delete-popup :url="route('admin.department.delete',$department->id)"/>
                @endcan
                @can('department-status-change')
                    <x-status.table.status-change :url="route('admin.department.status',$department->id)"/>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
