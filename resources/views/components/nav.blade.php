@php
    use App\CustomHelper;
@endphp
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-dark" data-widget="pushmenu" href="javascript:void(0)" role="button"><i
                    class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">
        <h6><img src="{{asset('images/happy.png')}}" alt="" style="height: 15px; width: 15px;">  Hello {{Auth::user()->first_name}}!</h6>
        <li class="nav-item dropdown px-2">
            <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false" title="Quick Add">
                <span class="icon plus-icon"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end text-sm medium radius-10">
                @php
                    $userType = Auth::user()->userType->type ?? null;
                    $loggedUsertypeSlug = Auth::user()->userType->slug ?? null;
                    $bookingRoute =
                        $userType === null || $userType === App\Models\UserType::ADMIN
                            ? route('bookings')
                            : route('create-booking');
                @endphp
                <a class="dropdown-item text-center" href="{{ $bookingRoute }}" title="Booking">Booking</a>
                @if ($userType === null || $userType === App\Models\UserType::ADMIN)
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item text-center" href="{{ route('create-driver') }}" title="Driver">Driver</a>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item text-center" href="{{ route('add-vehicle') }}" title="Vehicle">Vehicle</a>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item text-center" href="{{ route('client-create') }}" title="Client">Client</a>
                @endif
                @if ($loggedUsertypeSlug === null || in_array($loggedUsertypeSlug, ['admin', 'client-admin']))
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item text-center" href="{{ route('create-user') }}" title="Users">Users</a>
                @endif
            </div>
        </li>
        <li class="nav-item pe-4 dropdown"  data-notifications="{{ json_encode(Auth::user()->getUnreadNotificationsTop(10)) }}" id="notificationsContainer"></li>
        <li class="nav-item ps-3 dropdown">
            <button type="button" class="user-panel d-flex" data-bs-toggle="dropdown" aria-expanded="false"
                title="Profile">
                @if (Auth::user()->profile_image && Storage::disk('public')->exists(Auth::user()->profile_image))
                    <img id="navAvatar" src="{{ Storage::url(Auth::user()->profile_image) }}" class="img-circle"
                        alt="User">
                @else
                    <img id="navAvatar" src="{{ asset('images/profile.svg') }}" class="img-circle" alt="User">
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end text-sm medium radius-10">
                <button class="dropdown-item" type="button">
                    {{ CustomHelper::getFullName() }}
                </button>
                <div class="dropdown-divider m-0"></div>
                <a class="dropdown-item" href="{{ route('settings') }}" type="button">Account Settings</a>
                <div class="dropdown-divider m-0"></div>
                <a class="dropdown-item" href="{{ route('submit-logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('submit-logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="javascript:void(0)" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>
