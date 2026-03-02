<table class="dataTablesExample">
    <thead>
    @can('child-category-bulk-delete')
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
    @endcan
    <th>{{__('ID')}}</th>
    <th>{{__('Child Category')}}</th>
    <th>{{__('Image')}}</th>
    <th>{{__('Subcategory')}}</th>
    <th>{{__('Main Category')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Create Date')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
        @foreach($child_categories as $data)
            <tr>
                @can('child-category-bulk-delete')
                    <td>
                        <x-bulk-action.bulk-delete-checkbox :id="$data->id"/>
                    </td>
                @endcan
                <td>{{$data->id}}</td>
                <td>{{$data->name}}</td>
                <td>
                    {!! render_image_markup_by_attachment_id($data->image,' ','thumb') !!}
                </td>
                <td>{{ optional($data->subcategory)->name}}</td>
                <td>{{optional($data->category)->name}}</td>
                <td>
                    @if($data->status==1)
                        <span class="alert alert-success">{{__('Active')}}</span>
                    @else
                        <span class="alert alert-danger">{{__('Inactive')}}</span>
                    @endif
                    @can('child-category-status-change')
                         <span><x-status.status-change :url="route('admin.child.category.status',$data->id)"/></span>
                    @endcan
                </td>
                <td>{{date('d-m-Y', strtotime($data->created_at))}}</td>
                <td>
                    @can('child-category-delete')
                     <x-popup.delete-popup :url="route('admin.child.category.delete',$data->id)"/>
                    @endcan
                    @can('child-category-edit')
                    <x-icon.edit-icon :url="route('admin.child.category.edit',$data->id)"/>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $child_categories->links() }}
</div>
