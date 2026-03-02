<table class="dataTablesExample">
    <thead>
    @can('dynamic-page-bulk-delete')
    <th class="no-sort">
        <div class="mark-all-checkbox">
            <input type="checkbox" class="all-checkbox">
        </div>
    </th>
    @endcan
    <th>{{__('ID')}}</th>
    <th>{{__('Title')}}</th>
    <th>{{__('Date')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
    @foreach($all_pages as $data)
        <tr>
            @can('dynamic-page-bulk-delete')
            <td>
                <x-bulk-action.bulk-delete-checkbox :id="$data->id"/>
            </td>
            @endcan

            <td>{{$data->id}}</td>
            <td>{{$data->title ?? __('Untitled')}}
                @if($data->id == get_static_option('home_page'))
                    <strong class="text-primary">-{{__('Home Page')}}</strong>
                @endif
                @if($data->id == get_static_option('blog_page'))
                    <strong class="text-info">-{{__('Blog Page')}}</strong>
                @endif
            </td>
            <td>{{$data->created_at->diffForHumans()}}</td>
            <td><x-status.status-span :status="$data->status"/></td>
            <td>
                @if($data->id != get_static_option('home_page') && $data->id != get_static_option('blog_page'))
                    <x-languages.delete-popover-all-lang :url="route('admin.page.delete.lang.all',$data->id)"/>
                    <x-icon.view-icon :url="route('frontend.dynamic.page',$data->slug)"/>
                @endif
                @can('dynamic-page-edit')
                    <x-icon.edit-icon :url="route('admin.page.edit',$data->id).'?lang='.$default_lang"/>
                    @if(!empty($data->page_builder_status))
                        <a href="{{route('admin.dynamic.page.builder',['type' =>'dynamic-page','id' => $data->id])}}" target="_blank" class="cmnBtn btn_5 btn_bg_secondary radius-5">{{__('Open Page Builder')}}</a>
                    @endif
               @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $all_pages->links() }}
</div>
