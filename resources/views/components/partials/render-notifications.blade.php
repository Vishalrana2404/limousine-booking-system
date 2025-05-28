<a class="nav-link px-0" data-bs-toggle="dropdown" href="#" aria-expanded="false" title="Notification(s)">
    <span class="icon notification-icon">
        @if(Auth::user()->unreadNotifications()->count() > 0)
            <span class="notification notification_count">{{ Auth::user()->unreadNotifications()->count() < 10 ? Auth::user()->unreadNotifications()->count() : "9+" }}</span>
        @endif
    </span>
</a>
<div class="dropdown-menu dropdown-menu-xl py-2 notification-dropdown dropdown-menu-end text-xs medium radius-10">
    <span class="dropdown-item text-sm">
        <i class="fas fa-bell mr-2"></i> Notifications

        @if(Auth::user()->unreadNotifications()->count() > 0)
            <a href="javascript:void(0);" class="float-right text-primary unreadTopNotificationItem" title="Mark all Read" data-type="mark-all-read" data-id="">Mark all Read</a>
        @endif
    </span>
    <div class="dropdown-divider m-0"></div>

    @php
        $topNotifications = Auth::user()->getUnreadNotificationsTop(10);
    @endphp

    <div class="nav_unread_notifications">
        @forelse($topNotifications as $key => $topNotification)
            <a href="javascript:void(0);" class="dropdown-item text-dark bold unreadTopNotificationItem nav-bar-notification-item text-truncate"data-read-at="{{$topNotification->read_at}}"  data-id="{{$topNotification->id}}" data-type="mark-single-read">
              {{$topNotification->data['data']['message']}} for booking ID #{{$topNotification->data['data']['booking']['id']}}
            </a>
        @empty
            <p class="dropdown-item text-dark">No unread notifications found</p>
        @endforelse
    </div>
    
    <div class="dropdown-divider m-0"></div>
    <a class="dropdown-item text-center text-primary text-sm" href="{{ route('notifications') }}" title="View All Notifications">View All Notifications</a>
</div>