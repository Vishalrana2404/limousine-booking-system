<!-- Main Sidebar Container -->
@php
    $loggedUsertypeSlug = Auth::user()->userType->slug ?? null;
    $loggedUserDepartment = Auth::user()->department ?? null;
@endphp
<aside class="main-sidebar sidebar-light-primary border-right custom-sidebar">
    <!-- Brand Logo -->
    <a href="/" class="brand-link text-center" title="{{ config('app.name', 'Laravel') }}">
        <span class="icon-nav icon vehicles-icon"></span>
        <span class="brand-text text-sm semibold">{{ config('app.name', 'Laravel') }}</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}"
                        title="Dashboard">
                        <span class="icon-nav icon dash-icon"></span>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('bookings') }}" class="nav-link {{ Request::is('bookings*') ? 'active' : '' }}"
                        title="Bookings">
                        <span class="icon-nav icon bookings-icon"></span>
                        <p>Bookings</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('bookings-archives') }}" class="nav-link {{ Request::is('bookings-archives*') ? 'active' : '' }}"
                        title="Bookings Archives">
                        <span class="icon-nav icon bookings-archives-icon"></span>
                        <p>Bookings Archives</p>
                    </a>
                </li>
                @if ($loggedUsertypeSlug === null || in_array($loggedUsertypeSlug, ['admin', 'admin-staff']))
                <li class="nav-item">
                    <a href="{{ route('reports') }}" class="nav-link {{ Request::is('reports*') ? 'active' : '' }}"
                        title="Reports">
                        <span class="icon-nav icon reports-icon"></span>
                        <p>Reports</p>
                    </a>
                </li>
                @endif
                @if ($loggedUsertypeSlug === null || in_array($loggedUsertypeSlug, ['admin', 'admin-staff']))
                    <li class="nav-item {{ Request::is('driver*') ? 'menu-open' : '' }}">
                        <a href="javascript:void(0);" class="nav-link" title="Drivers">
                            <span class="icon icon-nav drive-icon"></span>
                            <p>
                                Drivers <i class="right fas fa-angle-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('drivers') }}"
                                    class="nav-link {{ Request::is('drivers') ? 'active' : '' }}"
                                    title="Driver Management">
                                    {{-- <span class="icon icon-nav report-icon"></span> --}}
                                    <p>Driver Management</p>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('drivers-off-day') ? 'active' : '' }}">
                                <a href="{{ route('drivers-off-day') }}" class="nav-link" title="Driver’s Off Day">
                                    {{-- <span class="icon icon-nav report-icon"></span> --}}
                                    <p>Driver’s Off Day</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('driver-schedule') }}"
                                    class="nav-link {{ Request::is('driver-schedule*') ? 'active' : '' }}"
                                    title="Driver's Schedule">
                                    {{-- <span class="icon icon-nav report-icon"></span> --}}
                                    <p>Driver's Schedule</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="nav-item {{ Request::is('vehicles*') || Request::is('vehicle-class*') ? 'menu-open' : '' }}">
                        <a href="javascript:void(0);" class="nav-link" title="Vehicles">
                            <span class="icon icon-nav vehicles-icon"></span>
                            <p>
                                Vehicles
                                <i class="right fas fa-angle-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item ">
                                <a href="{{ route('vehicles') }}"
                                    class="nav-link {{ Request::is('vehicles') ? 'active' : '' }}"
                                    title="Vehicle Management">
                                    {{-- <span class="icon icon-nav book-icon"></span> --}}
                                    <p>Vehicle Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('vehicle-class') }}"
                                    class="nav-link  {{ Request::is('vehicle-class') ? 'active' : '' }}"
                                    title="Vehicle Class">
                                    {{-- <span class="icon icon-nav book-icon"></span> --}}
                                    <p>Vehicle Class</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="nav-item {{ Request::is('hotels*') || Request::is('events*') ? 'menu-open' : '' }}">
                        <a href="javascript:void(0);" class="nav-link" title="Corporate">
                            <span class="icon icon-nav hotel-icon"></span>
                            <p>
                                Corporate
                                <i class="right fas fa-angle-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('hotels') }}" class="nav-link {{ Request::is('hotels*') ? 'active' : '' }}"
                                    title="Corporate">
                                    <span class="icon icon-nav"></span>
                                    <p>Corporate</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('events') }}" class="nav-link {{ Request::is('events*') ? 'active' : '' }}"
                                    title="Events">
                                    <span class="icon icon-nav"></span>
                                    <p>Events</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('clients') }}"
                            class="nav-link {{ Request::is('clients*') ? 'active' : '' }}" title="Clients">
                            <span class="icon icon-nav clients-icon"></span>
                            <p>Clients</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('logs*') ? 'active' : '' }}">
                        <a href="{{ route('logs') }}" class="nav-link" title="Logs">
                            <span class="icon icon-nav logs-icon"></span>
                            <p>Logs</p>
                        </a>
                    </li>
                @else
                <li
                    class="nav-item {{ Request::is('hotels*') || Request::is('events*') ? 'menu-open' : '' }}">
                    <a href="javascript:void(0);" class="nav-link" title="Corporate">
                        <span class="icon icon-nav hotel-icon"></span>
                        <p>
                            Corporate
                            <i class="right fas fa-angle-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('events') }}" class="nav-link {{ Request::is('events*') ? 'active' : '' }}"
                                title="Events">
                                <span class="icon icon-nav"></span>
                                <p>Events</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if ($loggedUserDepartment === null || in_array($loggedUserDepartment, ['Management', 'Finance', 'Supervisor']))
                    <li
                        class="nav-item {{ Request::is('email-templates*') ? 'menu-open' : '' }}">
                        <a href="javascript:void(0);" class="nav-link" title="Billing">
                            <span class="icon icon-nav billing-icon"></span>
                            <p>
                                Billing
                                <i class="right fas fa-angle-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('email-templates') }}" class="nav-link {{ Request::is('email-templates*') ? 'active' : '' }}"
                                    title="Email Templates">
                                    <span class="icon icon-nav"></span>
                                    <p>Email Templates</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if ($loggedUsertypeSlug === null || in_array($loggedUsertypeSlug, ['admin', 'client-admin']))
                    <li class="nav-item">
                        <a href="{{ route('users') }}" class="nav-link {{ Request::is('users*') ? 'active' : '' }}"
                            title="Users">
                            <span class="icon icon-nav user-icon"></span>
                            <p>Users</p>
                        </a>
                    </li>
                @endif
                @if (in_array($loggedUsertypeSlug, ['client-admin','client-staff']))
                    <li class="nav-item">
                        <a href="{{ route('terms-and-conditions') }}" class="nav-link {{ Request::is('terms-and-conditions*') ? 'active' : '' }}"
                            title="Terms and Conditions">
                            <span class="icon icon-nav user-icon"></span>
                            <p>Terms and Conditions</p>
                        </a>
                    </li>
                @endif
                <!-- Placeholder for other menu items -->
                <li class="nav-item {{ Request::is('settings*') || Request::is('peak-period*') || Request::is('city-surcharge*') ? 'menu-open' : '' }}">
                    <a href="javascript:void(0);" class="nav-link" title="Settings">
                        <span class="icon icon-nav setting-icon"></span>
                        <p>
                            Settings
                            <i class="right fas fa-angle-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('settings') }}"
                                class="nav-link {{ Request::is('settings') ? 'active' : '' }}"
                                title="Account Settings">
                                {{-- <i class="far fa-circle nav-icon"></i> --}}
                                <p>Account Settings</p>
                            </a>
                        </li>
                        <!-- Placeholder for other settings menu items -->
                    </ul>
                    @if ($loggedUsertypeSlug === null || in_array($loggedUsertypeSlug, ['admin', 'admin-staff']))
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('peak-period') }}"
                                    class="nav-link {{ Request::is('peak-period') ? 'active' : '' }}"
                                    title="Peak Period">
                                    {{-- <i class="far fa-circle nav-icon"></i> --}}
                                    <p>Peak Period</p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('city-surcharge') }}"
                                    class="nav-link {{ Request::is('city-surcharge*') ? 'active' : '' }}"
                                    title="Outside City Surcharge">
                                    {{-- <span class="far fa-circle nav-icon"></span> --}}
                                    <p>Outside City Surcharge</p>
                                </a>
                            </li>
                        </ul>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
</aside>
