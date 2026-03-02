<table class="dataTablesExample">
    <thead>
    @can('admin-listing-bulk-delete')
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
    @endcan
    <th>{{__('ID')}}</th>
    <th>{{__('Image')}}</th>
    <th>{{__('Title')}}</th>
    <th>{{__('Category')}}</th>
    @if(empty(get_static_option('google_map_settings_on_off')))
         <th>{{__('country')}}</th>
    @endif
    <th>{{__('Price')}}</th>
    <th>{{__('Admin Name')}}</th>
    <th>{{__('Crate Date')}}</th>
    <th>{{__('Published Date')}}</th>
    <th>{{__('Publishing Status')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
    @foreach($all_listings as $data)
        <tr>
            @can('admin-listing-bulk-delete')
                <td>
                    <x-bulk-action.bulk-delete-checkbox :id="$data->id"/>
                </td>
            @endcan
            <td>{{$data->id}}</td>
            <td> {!! render_image_markup_by_attachment_id($data->image,'','thumb') !!}</td>
            <td>{{$data->title}}</td>
            <td>{{optional($data->category)->name}}</td>
            @if(empty(get_static_option('google_map_settings_on_off')))
                <td>{{optional($data->country)->country}}</td>
            @endif
            <td>{{$data->price}}</td>
            <td>{{optional($data->admin)->name}}</td>
            <td>
                <strong class="subCap">{{ $data->created_at->diffForHumans() }}</strong>
            </td>
            <!--Publish -->
            <td>
                {{ $data->published_at ? \Carbon\Carbon::parse($data->published_at)->format('F j, Y') : __('Not published') }}
            </td>


            <!--published -->
            <td>
                @if($data->is_published === 1)
                    <span class="alert alert-success">{{__('Published')}}</span>
                @else
                    <span class="alert alert-warning">{{__('Unpublished')}}</span>
                @endif
                @can('admin-listing-published-status-change')
                     <span class="my-2"><x-status.admin-listing-published-change :url="route('admin.listings.published.status.change.by',$data->id)"/></span>
                @endcan
            </td>

            <!--status -->
            <td>
                @if($data->status==1)
                    <span class="alert alert-success">{{__('Approved')}}</span>
                @else
                    <span class="alert alert-warning">{{__('Pending')}}</span>
                @endif
                @can('admin-listing-status-change')
                  <span class="my-2"><x-status.status-change :url="route('admin.listings.status.change.by',$data->id)"/></span>
                @endcan
            </td>

            <!--Action -->
            <td>
                @can('admin-listing-edit')
                <x-icon.edit-icon :url="route('admin.edit.listing',$data->id)"/>
                @endcan
                <x-icon.view-icon :url="route('admin.listings.details',$data->id)"/>
                @can('admin-listing-delete')
                <x-popup.delete-popup :url="route('admin.listings.delete',$data->id)"/>
               @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $all_listings->links() }}
</div>
