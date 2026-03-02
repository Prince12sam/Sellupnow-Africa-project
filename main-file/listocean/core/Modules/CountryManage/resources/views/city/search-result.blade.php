<table class="DataTable_activation">
    <thead>
    <tr>
        @can('city-bulk-delete')
            <th class="no-sort">
                <div class="mark-all-checkbox">
                    <input type="checkbox" class="all-checkbox">
                </div>
            </th>
        @endcan
        <th>{{__('ID')}}</th>
        <th>{{__('City')}}</th>
        <th>{{__('State')}}</th>
        <th>{{__('Country')}}</th>
        <th>{{__('Status')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_cities as $city)
        <tr>
            @can('city-bulk-delete')
                <td>
                    <x-bulk-action.bulk-delete-checkbox :id="$city->id"/>
                </td>
            @endcan
            <td>{{ $city->id }}</td>
            <td>{{ $city->city }}</td>
            <td>{{ optional($city->state)->state }}</td>
            <td>{{ optional($city->country)->country }}</td>
            <td>
                <x-status.table.active-inactive :status="$city->status"/>
            </td>
            <td>
                    @can('city-edit')
                        <a class="cmnBtn btn_5 btn_bg_warning radius-5 edit_city_modal"
                           data-bs-toggle="modal"
                           data-bs-target="#editCityModal"
                           data-city="{{ $city->city }}"
                           data-city_id="{{ $city->id }}"
                           data-state_id="{{ $city->state_id }}"
                           data-country_id="{{ $city->country_id }}">
                            {{ __('Edit City') }}
                        </a>
                    @endcan
                    @can('city-delete')
                        <x-popup.delete-popup :title="__('Delete City')" :url="route('admin.city.delete',$city->id)"/>
                    @endcan
                    @can('city-status-change')
                       <x-status.table.status-change :title="__('Change Status')" :url="route('admin.city.status',$city->id)"/>
                    @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_cities"/>
