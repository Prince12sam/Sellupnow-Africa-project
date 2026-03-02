<table class="dataTablesExample">
    <thead>
    @can('subcategory-bulk-delete')
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
    @endcan

    <th>{{__('ID')}}</th>
    <th>{{__('Subcategory')}}</th>
    <th>{{__('Image')}}</th>
    <th>{{__('Main Category')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Create Date')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
    @foreach($sub_categories as $data)
        <tr>
            @can('subcategory-bulk-delete')
                <td>
                    <x-bulk-action.bulk-delete-checkbox :id="$data->id"/>
                </td>
            @endcan
            <td>{{$data->id}}</td>
            <td>{{$data->name}}</td>
            <td>
                {!! render_image_markup_by_attachment_id($data->image,' ','thumb') !!}
            </td>
            <td>{{optional($data->category)->name}}</td>
            <td>
                @if($data->status==1)
                    <span class="alert alert-success">{{__('Active')}}</span>
                @else
                    <span class="alert alert-danger">{{__('Inactive')}}</span>
                @endif
                @can('subcategory-status-change')
                      <span><x-status.status-change :url="route('admin.subcategory.status',$data->id)"/></span>
                @endcan
            </td>
            <td>{{date('d-m-Y', strtotime($data->created_at))}}</td>
            <td>
                @can('subcategory-delete')
                <x-popup.delete-popup :url="route('admin.subcategory.delete',$data->id)"/>
                @endcan
                @can('subcategory-edit')
                <x-icon.edit-icon :url="route('admin.subcategory.edit',$data->id)"/>
               @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $sub_categories->links() }}
</div>
