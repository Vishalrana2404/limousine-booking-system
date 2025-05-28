@forelse($notifications as $key => $notification)

   @include('components.partials.notification-row',['notification'=>$notification])
@empty
    <tr>
        <td class="text-center" colspan="5">No notifications found</td>
    </tr>
@endforelse