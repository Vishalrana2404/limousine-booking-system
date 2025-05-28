
 <tr id="{{$notification->id}}" class="unreadNotificationItem @if(empty($notification->read_at)) bold @endif" data-type="mark-single-read" data-id="{{$notification->id}}" data-read-at="{{$notification->read_at}}">
        <td>{{$notification->data['data']['message']}} for booking #{{$notification->data['data']['booking']['id']}}</td>
        <td>{{ App\CustomHelper::parseDateTime(App\CustomHelper::formatSingaporeDate($notification->created_at), "d M, Y") ?? 'N/A' }}</td>
        <td>{{ App\CustomHelper::parseDateTime(App\CustomHelper::formatSingaporeDate($notification->created_at), "H:i") ?? 'N/A' }}</td>
        <td>{{$notification->data['data']['from_user_name']}}</td>
    </tr>