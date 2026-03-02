<table class="dataTablesExample">
    <thead>
    @can('advertisement-delete')
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
    @endcan
    <th>{{__('ID')}}</th>
    <th>{{__('Title')}}</th>
    <th>{{__('Type')}}</th>
    <th>{{__('Size')}}</th>
    <th>{{__('Image')}}</th>
    <th>{{__('Click')}}</th>
    <th>{{__('Impression')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
    @if(!empty($all_advertisements) && $all_advertisements->count())
        @foreach($all_advertisements as $data)
            <tr>
                @can('advertisement-delete')
                    <td>
                        <x-bulk-action.bulk-delete-checkbox :id="$data->id"/>
                    </td>
                @endcan
                <td>{{$data->id}}</td>
                <td>{{$data->title}}</td>
                <td>{{__(str_replace('_',' ',$data->type))}}</td>
                <td>{{$data->size}}</td>
                <td>
                    @php
                        $add_img = get_attachment_image_by_id($data->image,null,true);
                    @endphp
                    @if (!empty($add_img))
                        <div class="attachment-preview">
                            <div class="thumbnail">
                                <div class="centered">
                                    <img class="avatar user-thumb" src="{{$add_img['img_url']}}" alt="">
                                </div>
                            </div>
                        </div>
                    @endif
                </td>
                <td>{{$data->click}}</td>
                <td>{{$data->impression}}</td>
                <td>
                    @if($data->status==1)
                        <span class="alert alert-success">{{__('Active')}}</span>
                    @else
                        <span class="alert alert-danger">{{__('Inactive')}}</span>
                    @endif
                    @can('advertisement-status-change')
                         <span><x-status.status-change :url="route('admin.advertisement.status',$data->id)"/></span>
                    @endcan
                </td>
                <td>
                    @can('advertisement-delete')
                        <x-popup.delete-popup :url="route('admin.advertisement.delete',$data->id)"/>
                    @endcan
                    @can('advertisement-edit')
                        <x-btn.edit :url="route('admin.advertisement.edit',$data->id)"/>
                    @endcan
                </td>
            </tr>
        @endforeach
    @else
        <span>{{ __('Advertisement No Found') }}</span>
    @endif
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $all_advertisements->links() }}
</div>
