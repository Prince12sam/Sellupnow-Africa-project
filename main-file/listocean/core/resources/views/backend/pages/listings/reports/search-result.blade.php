<table class="DataTable_activation">
    <thead>
    <tr>
        @can('listing-report-bulk-delete')
            <th class="no-sort">
                <div class="mark-all-checkbox">
                    <input type="checkbox" class="all-checkbox">
                </div>
            </th>
        @endcan
        <th>{{__('ID')}}</th>
        <th>{{__('User Name')}}</th>
        <th>{{__('Listing Title')}}</th>
        <th>{{__('Reason')}}</th>
        <th>{{__('Description')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_reports as $data)
        @if($data->listing)
            <tr>
                @can('listing-report-bulk-delete')
                    <td> <x-bulk-action.bulk-delete-checkbox :id="$data->id"/> </td>
                @endcan
                <td>{{ $data->id }}</td>
                <td class="text-primary"><a href="{{route('about.user.profile', $data?->user->username)}}" target="_blank">{{ $data?->user->fullname }}</a></td>
                <td class="text-primary">
                    <a  href="{{route('frontend.listing.details', $data?->listing->slug)}}" target="_blank">
                        {{ $data?->listing->title }}
                    </a>
                </td>
                <td> {{ $data?->reason->title }} </td>
                <td>
                    <button class="cmnBtn btn_5 btn_bg_info radius-5 view-description" data-description="{{ $data->description }}">{{ __('View Description') }}</button>
                </td>
                <td>
                    @can('listing-report-delete')
                        <x-popup.delete-popup :url="route('admin.listing.report.delete',$data->id)"/>
                    @endcan
                </td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_reports"/>
