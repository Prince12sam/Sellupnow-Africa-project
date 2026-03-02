<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('Support Ticket Email to Admin')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('This email will send to user when the admin send a support ticket message. Also send to admin when a user send a support ticket message.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.user.support.ticket.to.admin.template')"/>
    </td>
</tr>

<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('Support Ticket Email to User')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('This email will send to user when the admin create a ticket. Also send to admin when a user create a ticket.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.user.support.ticket.to.user.template')"/>
    </td>
</tr>
