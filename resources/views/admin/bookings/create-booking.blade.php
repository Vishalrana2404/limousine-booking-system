@extends('components.layout')
@section('content')
<div class="content-wrapper">
    <section class="content-header border-bottom">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <a class="dark-color back-btn" href="{{ route('bookings') }}" title="Bookings">
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect x="25" y="25" width="25" height="25" transform="rotate(-180 25 25)" fill="url(#pattern0_31_250)" />
                            <defs>
                                <pattern id="pattern0_31_250" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_31_250" transform="scale(0.0078125)" />
                                </pattern>
                                <image id="image0_31_250" width="128" height="128" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJwSURBVHic7d09axRRGIbhe+aAla3B0r8hYmcjGFJbib1so+IXaBtN/GhUrPwW7ESsxcJC1F9gof4CESIKIo7FoASzS3Zmc+bN5twXnDLDA+/DJpmdOQckSZIkSZIkSZIkSZIkSZIkSZIkSZIkSZIkbVABx4G3wA/gK/ACOBAZSsOogUdAM2b9AkZx0TSEEeOHv36dDEun7D6xeQEa4FRUQOWzh+mG/3edjompXLoWoAHOhCRVNtP+Cli/zuYKk3JdWBP9Bg53/JlDwE/g9dbH0dBq4CndPwUa4HxAXmWQgMf0K8HFgLzKYJYSXArIqwwSk+8KWoJCJOAh/UqwHJBXGVgCWQK1JXhAvxJcDsirDBJwn34luBKQVxlYApGAe/QrwcrwcZVDTf8SrA4fVznUwF0sQdEq4Db9SnA1IK8ysASiAm7RrwTXAvIqgwq4Sb8SXA/IqwxmLUE17qJLwCvgW88Lu+Zn3eC/Eixvg1CuYdcKtC1YAp6hEi0m4A6wLziIYuytgDVgd3QShViroxMoVFMD76NTKMy7BHwBjkYnUYhRAj4Au4CDwWE0rFXam0n/HAFe0v5RGP0/qivv2nAjSPPF7wMK5jeCBZvlmQCHP+d8IKRgPhdYMJ8MLpjvBhTMt4MK5vAL5hvCBZtljwCHP+fcIKJgDr9g7hRWMPcKLJi7hRYsAU9w+EVys+jCTXNm0Lh1LiKstt5nug8/24ERGtYC3Yd/ISSpsvDQKPGR6YbvsXE71Ak2H74HR+5gFZPv+3t0bCEq4BjwBvhO+37mc2B/ZChJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRtQ38A9GwPyk+0wKwAAAAASUVORK5CYII=" />
                            </defs>
                        </svg>
                        Bookings
                    </a>
                    <h6 class="head-sm medium">New Booking</h6>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content-header border-bottom">
        <ul class="nav nav-underline" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link text-dark {{ !old('multiple_booking') ? 'active' : '' }}" id="pills-single-booking-tab" data-bs-toggle="pill" data-bs-target="#pills-single-booking" type="button" role="tab" aria-controls="pills-single-booking" aria-selected="true">Single Booking</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-dark {{ old('multiple_booking') ? 'active' : '' }}" id="pills-multiple-booking-tab" data-bs-toggle="pill" data-bs-target="#pills-multiple-booking" type="button" role="tab" aria-controls="pills-multiple-booking" aria-selected="false">Multiple Booking</a>
            </li>
        </ul>
    </section>
    <section class="content">
        <div class="card no-box-shadow">
            <div class="card-body p-0">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade {{ !old('multiple_booking') ? 'show active' : '' }}" id="pills-single-booking" role="tabpanel" aria-labelledby="pills-single-booking-tab">
                        @include('admin.bookings.partials.single-booking')
                    </div>
                    <div class="tab-pane fade  {{ old('multiple_booking') ? 'show active' : '' }}" id="pills-multiple-booking" role="tabpanel" aria-labelledby="pills-multiple-booking-tab">
                        <div class="text-end mb-4">
                            <button type="button" id="addNewRow" class="inner-theme-btn" title="Add New Row">
                                <i class="fa fa-solid fa-plus"></i> Add New Row</button>
                        </div>
                        @include('admin.bookings.partials.multiple-booking')
                    </div>
                </div>
            </div>
        </div>
    </section>
      @include('modals.blackout-period-modal')
</div>
<style>
.pac-container{
    margin-top:-55px;
}
</style>
@vite(['resources/js/Bookings.js'])
<script>
    const props = {
        routes: {
            getEventsByHotel: "{{ route('get-hotel-events') }}",
            createEventByAjax: "{{ route('create-event-by-ajax') }}",
        },
        isCreatePage:true,
        serviceTypes: @json($serviceTypes),
        vehicleTypes: @json($vehicleTypes),
        locations: @json($locations),
        peakPeriods: @json($peakPeriods),
        hotelClients:@json($hotelClients),
        multipleCorporatesHotelData: @json($multipleCorporatesHotelData),
        events:@json($events),
        loggedUser:@json(Auth::user()),
    }
</script>
@endsection