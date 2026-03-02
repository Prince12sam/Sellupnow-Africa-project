<table class="dataTablesExample">
    <thead>
    @can('tag-bulk-delete')
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
    @endcan
    <th>{{__('ID')}}</th>
    <th>{{__('Name')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
    @if(!empty($all_tags) && $all_tags->count())
        @foreach($all_tags as $data)
            <tr>
                @can('tag-bulk-delete')
                    <td>
                        <div class="bulk-checkbox-wrapper">
                            <input type="checkbox" class="bulk-checkbox" name="bulk_delete[]" value="{{$data->id}}">
                        </div>
                    </td>
                @endcan
                <td>{{$data->id}}</td>
                <td>{{$data->name }}</td>
                <td>
                    @if($data->status == 'draft')
                        <span class="alert alert-primary" >{{__('Draft')}}</span>
                    @else
                        <span class="alert alert-success" >{{__('Publish')}}</span>
                    @endif
                </td>
                <td>
                    @can('tag-bulk-delete')
                        <x-languages.delete-popover-all-lang :url="route('admin.blog.tags.delete.all.lang',$data->id)"/>
                    @endcan
                    @can('tag-edit')
                        <a href="#"
                           data-bs-toggle="modal"
                           data-bs-target="#category_edit_modal"
                           class="btn btn-lg btn-primary btn-sm category_edit_btn"
                           data-id="{{$data->id}}"
                           data-name="{{$data->name}}"
                           data-status="{{$data->status}}"
                           data-slug="{{$data->slug}}">
                            <i class="ti-pencil"></i>
                        </a>
                    @endcan
                </td>
            </tr>
        @endforeach
    @else
        <span>{{ __('Tag No Found') }}</span>
    @endif
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $all_tags->links() }}
</div>
