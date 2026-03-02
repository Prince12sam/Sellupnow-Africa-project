<table class="DataTable_activation">
    <thead>
    <tr>
        @can('report-reason-bulk-delete')
            <th class="no-sort">
                <div class="mark-all-checkbox">
                    <input type="checkbox" class="all-checkbox">
                </div>
            </th>
        @endcan
        <th>{{__('ID')}}</th>
        <th>{{__('Title')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_reasons as $data)
        <tr>
            @can('report-reason-bulk-delete')
                <td> <x-bulk-action.bulk-delete-checkbox :id="$data->id"/> </td>
            @endcan
            <td>{{ $data->id }}</td>
            <td>{{ $data->title }}</td>
            <td>
                @can('report-reason-edit')
                    <a class="cmnBtn btn_5 btn_bg_warning btnIcon radius-5 edit_reason_modal"
                        data-bs-toggle="modal"
                        data-bs-target="#editReasonModal"
                        data-id="{{ $data->id }}"
                        data-title="{{ $data->title }}">
                       <i class="las la-pen"></i>
                    </a>
                @endcan

                @can('report-reason-delete')
                <x-popup.delete-popup :url="route('admin.report.reason.delete',$data->id)"/>
                @endcan

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_reasons"/>
