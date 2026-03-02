<table class="dataTablesExample">
    <thead>
    <th>{{__('ID')}}</th>
    <th>{{__('Title')}}</th>
    <th>{{__('Description')}}</th>
    <th>{{__('Notice Type')}}</th>
    <th>{{__('Notice For')}}</th>
    <th>{{__('Expire Date')}}</th>
    <th>{{__('Status')}}</th>
    <th>{{__('Action')}}</th>
    </thead>
    <tbody>
    @foreach($notices as $data)
        <tr>
            <td>{{$data->id}}</td>
            <td>{{$data->title}}</td>
            <td>{!! $data->description !!}</td>
            <td>
                @if($data->notice_type === 1)
                    <span class="text-danger">{{ __('Error') }}</span>
                @elseif($data->notice_type === 2)
                    <span class="text-warning">{{ __('Warning') }}</span>
                @elseif($data->notice_type === 3)
                    <span class="text-success">{{ __('Success') }}</span>
                @elseif($data->notice_type === 4)
                    <span class="text-info">{{ __('Info') }}</span>
                @endif
            </td>
            <td>
                @if($data->notice_for === 1)
                    {{ __('Frontend') }}
                @elseif($data->notice_for === 2)
                    {{ __('Buyer Dashboard') }}
                @elseif($data->notice_for === 3)
                    {{ __('Seller Dashboard') }}
                @endif
            </td>
            <td>
                @if(!empty($data->expire_date))
                    {{ date('d-m-Y', strtotime($data->expire_date)) }}
                @else
                    {{ __('Date not available') }}
                @endif
            </td>
            <td width="200px">
                @if($data->status==1)
                    <span class="cmnBtn btn_5 btn_bg_green radius-5">{{__('Active')}}</span>
                @else
                    <span class="cmnBtn btn_5 btn_bg_danger radius-5">{{__('Inactive')}}</span>
                @endif
                @can('notice-status-change')
                        <span class="my-2"><x-status.status-change :url="route('admin.notice.status',$data->id)"/></span>
                @endcan
            </td>
            <td width="200px">
                @can('notice-delete')
                    <x-popup.delete-popup :url="route('admin.delete.notice',$data->id)"/>
                @endcan
                @can('notice-edit')
                <x-icon.edit-icon :url="route('admin.notice.edit',$data->id)"/>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $notices->links() }}
</div>
