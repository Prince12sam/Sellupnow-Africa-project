<table class="dataTablesExample">
    <thead>
    @can('blog-bulk-delete')
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
    @endcan
    <th>{{__('ID')}}</th>
    <th>{{__('Title')}}</th>
    <th>{{__('Image')}}</th>
    <th>{{__('Category')}}</th>
    <th>{{__('Author')}}</th>
    <th>{{__('Created By')}}</th>
    <th>{{__('Views')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Date')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
    @foreach($blogs as $data)
        <tr>
            @can('blog-bulk-delete')
                <td>
                    <x-bulk-action.bulk-delete-checkbox :id="$data->id"/>
                </td>
            @endcan
            <td>{{$data->id}}</td>
            <td>{{$data->title}}</td>
            <td> {!! render_image_markup_by_attachment_id($data->image,' ','thumb') !!}</td>
            <td>{{optional($data->category)->name}}</td>
            <td>{{ $data->author_data()->name ?? __('Anonymous') }}</td>
            <td>{{ $data->created_by  ?? __('Anonymous') }}</td>
            <td>{{ $data->views }}</td>
            <td>
                @if($data->status == 'draft')
                    <span class="alert alert-danger">{{ $data->status }}</span>
                @elseif($data->status == 'publish')
                    <span class="alert alert-success">{{ $data->status }}</span>
                @elseif($data->status == 'archive')
                    <span class="alert alert-warning">{{ $data->status }}</span>
                @else
                    <span class="alert alert-info">{{ $data->status }}</span>
                @endif
            </td>
            <td>{{ $data->created_at,'d-M-Y' }}</td>
            <td>
                <x-icon.view-icon :url="route('frontend.blog.single',$data->slug)"/>
                @can('blog-delete')
                    <x-popup.delete-popup :url="route('admin.blog.delete.all.lang',$data->id)"/>
                @endcan
                @can('blog-edit')
                    <x-icon.edit-icon :url="route('admin.blog.edit',$data->id)"/>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $blogs->links() }}
</div>
