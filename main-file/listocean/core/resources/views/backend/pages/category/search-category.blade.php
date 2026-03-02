<table class="dataTablesExample">
    <thead>
    @can('category-bulk-delete')
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
    @endcan
    <th>{{__('ID')}}</th>
    <th>{{__('Category')}}</th>
    <th>{{__('Icon')}}</th>
    <th>{{__('Image')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Create Date')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
    @if(!empty($categories) && $categories->count())
        @foreach($categories as $data)
            <tr>
                @can('category-bulk-delete')
                    <td>
                        <x-bulk-action.bulk-delete-checkbox :id="$data->id"/>
                    </td>
                @endcan
                <td>{{$data->id}}</td>
                <td>{{$data->name}}</td>
                <td><i class="{{$data->icon}} btn btn-primary"></i></td>
                <td>
                    {!! render_image_markup_by_attachment_id($data->image,' ','thumb') !!}
                </td>
                <td>
                    @if($data->status==1)
                        <span class="alert alert-success">{{__('Active')}}</span>
                    @else
                        <span class="alert alert-danger">{{__('Inactive')}}</span>
                    @endif
                    @can('category-status-change')
                            <span><x-status.status-change :url="route('admin.category.status',$data->id)"/></span>
                    @endif
                </td>
                <td>{{date('d-m-Y', strtotime($data->created_at))}}</td>
                <td>
                    @can('category-edit')
                        <x-icon.edit-icon :url="route('admin.category.edit',$data->id)"/>
                   @endcan
                    @can('category-delete')
                        <x-popup.delete-popup :url="route('admin.category.delete',$data->id)"/>
                   @endcan
                </td>
            </tr>
        @endforeach
    @else
        <span>{{ __('Category No Found') }}</span>
    @endif
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $categories->links() }}
</div>
