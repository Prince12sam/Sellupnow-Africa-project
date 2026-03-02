<table class="DataTable_activation">
    <thead>
    <tr>
        @can('country-bulk-delete')
            <th class="no-sort">
                <div class="mark-all-checkbox">
                    <input type="checkbox" class="all-checkbox">
                </div>
            </th>
        @endcan
        <th>{{__('ID')}}</th>
        <th>{{__('Country')}}</th>
        <th>{{__('Country Code')}}</th>
        <th>{{__('Dial Code')}}</th>
        <th>{{__('Flag')}}</th>
        <th>{{__('Status')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_countries as $country)
        <tr>
            @can('country-bulk-delete')
                <td> <x-bulk-action.bulk-delete-checkbox :id="$country->id"/> </td>
            @endcan
            <td>{{ $country->id }}</td>
            <td>{{ $country->country }}</td>
            <td>{{ $country->country_code }}</td>
            <td>{{ $country->dial_code }}</td>
            <td>
                {!! render_country_flag_markup_by_attachment_url($country->country_code) !!}
            </td>
            <td><x-status.table.active-inactive :status="$country->status"/></td>
            <td>
                @can('country-edit')
                    <a class="cmnBtn btn_5 btn_bg_warning radius-5 edit_country_modal"
                        data-bs-toggle="modal"
                        data-bs-target="#editCountryModal"
                        data-country="{{ $country->country }}"
                        data-country_id="{{ $country->id }}">
                        {{ __('Edit Country') }}
                    </a>
                @endcan
                @can('country-delete')
                    <x-popup.delete-popup :title="__('Delete Country')" :url="route('admin.country.delete',$country->id)"/>
                @endcan
                @can('country-status-change')
                    <x-status.table.status-change :title="__('Change Status')" :url="route('admin.country.status',$country->id)"/>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_countries"/>
