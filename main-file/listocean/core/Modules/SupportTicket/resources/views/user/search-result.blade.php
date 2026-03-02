@if($tickets->count() > 0)
    <div class="custom_table">
        <table class="table mt-3">
            <thead class="table-light">
            <tr>
                <th>{{__('ID')}}</th>
                <th>{{__('Title')}}</th>
                <th>{{__('Priority')}}</th>
                <th>{{__('Status')}}</th>
                <th>{{__('Action')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tickets as $ticket)
                <tr class="table_row">
                    <td>{{ $ticket->id }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>
                        <div class="dashboard_table__main__priority">
                            @if($ticket->priority=='low') <a href="javascript:void(0)" class="priorityBtn pending">{{ __(ucfirst($ticket->priority)) }}</a>@endif
                            @if($ticket->priority=='high') <a href="javascript:void(0)" class="priorityBtn high">{{ __(ucfirst($ticket->priority)) }}</a>@endif
                            @if($ticket->priority=='medium') <a href="javascript:void(0)" class="priorityBtn medium">{{ __(ucfirst($ticket->priority)) }}</a>@endif
                            @if($ticket->priority=='urgent') <a href="javascript:void(0)" class="priorityBtn urgent">{{ __(ucfirst($ticket->priority)) }}</a>@endif
                        </div>
                    </td>
                    <td>
                        @if($ticket->status === 'open')
                            <span class="status accepted-status" >{{__('Open')}}</span>
                        @else
                            <span class="status cancel-status" >{{__('Close')}}</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-wrapper mb-20">
                         <a class="red-global-btn" href="{{ route('user.ticket.details',$ticket->id) }}"><i class="las la-eye"></i></a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="deposit-history-pagination mt-4">
        <x-pagination.laravel-paginate :allData="$tickets"/>
    </div>
@else
    <x-pagination.empty-data-placeholder :title="__('No Ticket Created Yet')"/>
@endif
