<table class="DataTable_activation">
    <thead>
    <tr>
        @can('state-bulk-delete')
            <th class="no-sort">
                <div class="mark-all-checkbox">
                    <input type="checkbox" class="all-checkbox">
                </div>
            </th>
        @endcan
        <th>{{__('ID')}}</th>
        <th>{{__('State')}}</th>
        <th>{{__('Country')}}</th>
        <th>{{__('Status')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_states as $state)
        <tr>
            @can('state-bulk-delete')
                <td> <x-bulk-action.bulk-delete-checkbox :id="$state->id"/> </td>
            @endcan

            <td>{{ $state->id }}</td>
            <td>{{ $state->state }}</td>
            <td>{{ optional($state->country)->country }}</td>
            <td><x-status.table.active-inactive :status="$state->status"/></td>
            <td>
                    @can('state-edit')
                    <a
                        class="cmnBtn btn_5 btn_bg_warning radius-5 edit_state_modal"
                        data-bs-toggle="modal"
                        data-bs-target="#editStateModal"
                        data-state_id="{{ $state->id }}"
                        data-state="{{ $state->state }}"
                        data-country="{{ $state->country_id }}"
                        data-timezone="{{ $state->timezone }}">
                        {{ __('Edit State') }}
                    </a>
                    @endcan
                    @can('state-delete')
                    <x-popup.delete-popup :title="__('Delete State')" :url="route('admin.state.delete',$state->id)"/>
                    @endcan
                    @can('state-status-change')
                    <x-status.table.status-change :title="__('Change Status')" :url="route('admin.state.status',$state->id)"/>
                    @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_states"/>
