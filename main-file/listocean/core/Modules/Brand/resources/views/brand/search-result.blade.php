<table class="DataTable_activation">
    <thead>
    <tr>
        @can('brand-bulk-delete')
            <th class="no-sort">
                <div class="mark-all-checkbox">
                    <input type="checkbox" class="all-checkbox">
                </div>
            </th>
        @endcan
        <th>{{__('ID')}}</th>
        <th>{{__('Brand')}}</th>
        <th>{{__('Image')}}</th>
        <th>{{__('Status')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_brands as $data)
        <tr>
            @can('brand-bulk-delete')
                <td> <x-bulk-action.bulk-delete-checkbox :id="$data->id"/> </td>
            @endcan
            <td>{{ $data->id }}</td>
            <td>{{ $data->title }}</td>
            <td>
                {!! render_image_markup_by_attachment_id($data->image) !!}
                @php $cat_img = get_attachment_image_by_id($data->image,null,true); @endphp
                @if (!empty($cat_img))
                    @php  $img_url = $cat_img['img_url']; @endphp
                @endif
            </td>
            <td><x-status.table.active-inactive :status="$data->status"/></td>
            <td>
                @can('brand-edit')
                <a class="cmnBtn btn_5 btn_bg_warning radius-5 edit_brand_modal"
                    data-bs-toggle="modal"
                    data-bs-target="#editBrandModal"
                    data-brand="{{$data->title}}"
                    data-url="{{$data->url}}"
                    data-img_id="{{ $data->image }}"
                    data-img_url="{{ $img_url }}"
                    data-brand_id="{{$data->id}}">
                    <i class="las la-pen"></i>
                </a>
                @endcan
                @can('brand-delete')
                <x-popup.delete-popup :url="route('admin.brand.delete',$data->id)"/>
                @endcan
                @can('brand-status-change')
                <x-status.table.status-change :url="route('admin.brand.status',$data->id)"/>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_brands"/>
