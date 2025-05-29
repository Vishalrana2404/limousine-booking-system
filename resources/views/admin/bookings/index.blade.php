@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        @php
            $user = Auth::user();
            $userTypeSlug = $user->userType->slug ?? null;
        @endphp
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-2">
                    @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                        <div class="col-xl-2">
                        @else
                            <div class="col-xl-4">
                    @endif
                    <h1 class="semibold head-sm">Bookings</h1>
                    <p class="normal text-xs">Booking Management</p>
                </div>
                <div class="col-xl-1">
                    <input type="text" id="search_by_booking_id" name="search_by_booking_id" class="form-control" placeholder="Booking Id">
                </div>
                <div class="col-xl-2">
                    <input type="text" id="search" name="search" class="form-control" placeholder="Search">
                </div>
                <div class="col-xl-2">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                        </div>
                        <input type="text" id="pickupDateBooking" class="form-control" placeholder="Select date"
                            autocomplete="off" autofocus />
                    </div>
                </div>
                @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                    <div class="col-xl-2">
                        <select id="driversList" class="form-control form-select custom-select">
                            <option value="">Select</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-xl-3">
                    <div class="d-flex flex-wrap gap-3 justify-content-end">
                        @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                            <button class="float-md-right inner-theme-btn bg-danger px-2" id="bulkDelete"
                                title="Bulk Delete">
                                <i class="fa fa-solid fa-trash"></i> Bulk Delete</button>
                        @endif
                        <a class="float-md-right inner-theme-btn px-2" href="{{ route('create-booking') }}"
                            title="Add Booking">
                            <i class="fa fa-solid fa-plus"></i> Add Booking</a>
                    </div>
                </div>

            </div>
    </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content bg-white pb-0">
        <!-- jquery validation -->
        <div class="card m-0">

            <input type="hidden" id="sortOrder">

            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="table-responsive custom-table table-scroll-height">
                    <table id="bookingTable" class="table table-head-fixed text-nowrap table-hover m-0 large-table">
                        <thead>
                            <tr>
                                @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                                    <th style="width: 10px">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input bookingTableCheckbox" type="checkbox"
                                                id="bulkBookingAction">
                                            <label for="bulkBookingAction" class="custom-control-label"></label>
                                        </div>
                                    </th>
                                @endif
                                <th>Action</th>
                                <th>Booking <i class="fa fa-sort ml-1 text-dark" id="sortBooking" aria-hidden="true"></i>
                                </th>
                                <th>Pickup Date<i class="fa fa-sort ml-1 text-dark" id="sortPickUpDate"
                                        aria-hidden="true"></i></th>
                                <th>Pickup Time<i class="fa fa-sort ml-1 text-dark" id="sortTime" aria-hidden="true"></i>
                                </th>
                                <th>Type<i class="fa fa-sort ml-1 text-dark" id="sortType" aria-hidden="true"></i></th>
                                <th>Pick-up<i class="fa fa-sort ml-1 text-dark" id="sortPikUp" aria-hidden="true"></i>
                                </th>
                                <th>Drop-off<i class="fa fa-sort ml-1 text-dark" id="sortDropOf" aria-hidden="true"></i>
                                </th>
                                <th>Guest Name</th>
                                @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                                    <th>Client<i class="fa fa-sort ml-1 text-dark" id="sortClient" aria-hidden="true"></i>
                                    </th>
                                @endif
                                <th>Contact<i class="fa fa-sort ml-1 text-dark" id="sortContact" aria-hidden="true"></i>
                                </th>
                                <th>Driver<i class="fa fa-sort ml-1 text-dark" id="sortDriver" aria-hidden="true"></i>
                                </th>
                                <th>Vehicle<i class="fa fa-sort ml-1 text-dark" id="sortVehicleType" aria-hidden="true"></i>
                                </th>
                                <th>Class<i class="fa fa-sort ml-1 text-dark" id="sortVehicleType" aria-hidden="true"></i>
                                </th>
                                <th>Status<i class="fa fa-sort ml-1 text-dark" id="sortStatus" aria-hidden="true"></i>
                                </th>
                                <th>Client Instructions<i class="fa fa-sort ml-1 text-dark" id="sortInstructions"
                                        aria-hidden="true"></i></th>
                                <!-- <th>Booked On<i class="fa fa-sort ml-1 text-dark" id="sortBookingDate"
                                        aria-hidden="true"></i></th> -->
                                <!-- @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                                    <th>Driver Remarks<i class="fa fa-sort ml-1 text-dark" id="sortDriverRemark"
                                            aria-hidden="true"></i></th>
                                    <th>Dispatch</th>
                                    <th>Comment<i class="fa fa-sort ml-1 text-dark" id="sortComment" aria-hidden="true"></i>
                                    </th> -->
                                    <!-- <th>Last Edit<i class="fa fa-sort ml-1 text-dark" id="sortLastEdit"
                                            aria-hidden="true"></i></th> -->
                                <!-- @endif -->
                            </tr>
                        </thead>
                        <tbody>
                            @include('admin.bookings.partials.booking-listing')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @include('modals.dispatch-model')
    </div>
    <style>
        .pac-container {
            margin-top: -55px;
        }
    </style>
    @vite(['resources/js/Bookings.js'])
    <script>
        const props = {
            routes: {
                filterBookings: "{{ route('filter-bookings') }}",
                updateDispatch: "{{ route('update-dispatch') }}",
                updateBookings: "{{ route('update-inline-booking') }}",
                deleteBookings: "{{ route('delete-bookings') }}"
            },
            locations: @json($locations),
            hotels: @json($hotels),
            drivers: @json($drivers),
            vehicles: @json($vehicles),
            vehicleTypes: @json($vehicleTypes),
            driverOffDays: @json($driverOffDays),
            userTypeId: @json($user->user_type_id)
        }
    </script>
@endsection
