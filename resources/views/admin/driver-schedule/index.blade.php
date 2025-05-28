@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        @php
            $user = Auth::user();
        @endphp
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Drivers</h1>
                        <p class="normal text-xs">Driver's Schedule</p>
                    </div>
                    <div class="col-sm-2">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"
                            title="Hide Contact">
                            <input type="checkbox" class="custom-control-input" id="hideContact" checked="">
                            <label class="custom-control-label pb-0" for="hideContact">Hide/Show Contact</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                            </div>
                            <input type="text" id="pickupDate" class="form-control" placeholder="Select date"
                                autocomplete="off" autofocus />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="driversList" class="form-control form-select custom-select">
                            <option value="">Select</option>
                            @foreach ($driverData as $drivers)
                                <option value="{{ $drivers->id }}">{{ $drivers->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1" id="exportOptions">
                        @if (!$driversBooking->isEmpty())
                            <select id="exportFormat" class="form-control form-select custom-select">
                                <option value="">Export</option>
                                <option value="pdf">Image</option>
                                <option value="excel">Excel</option>
                            </select>
                        @endIf
                    </div>
                </div>
            </div>
        </section>
        <section class="content bg-white">
            <div class="card">
                <div class="card-header border-0">
                    <!-- <div class="card-tools">
                                                            <span class="icon setting-icon"></span>
                                                        </div> -->
                </div>
                <input type="hidden" id="sortOrder">
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table id="driverScheduleTable" class="table table-head-fixed text-nowrap table-hover m-0">
                            <thead>
                                <tr>
                                    <th>Booking <i class="fa fa-sort ml-1 theme-color" id="sortBooking"
                                            aria-hidden="true"></i></th>
                                    <th>Time<i class="fa fa-sort ml-1 theme-color" id="sortTime" aria-hidden="true"></i>
                                    </th>
                                    <th>Type<i class="fa fa-sort ml-1 theme-color" id="sortType" aria-hidden="true"></i>
                                    </th>
                                    <th>Pick-up<i class="fa fa-sort ml-1 theme-color" id="sortPickUp"
                                            aria-hidden="true"></i></th>
                                    <th>Drop-off<i class="fa fa-sort ml-1 theme-color" id="sortDropOff"
                                            aria-hidden="true"></i></th>
                                    <th>Guest Name<i class="fa fa-sort ml-1 theme-color" id="sortGuestName"
                                            aria-hidden="true"></i></th>
                                    <th>Client<i class="fa fa-sort ml-1 theme-color" id="sortClient" aria-hidden="true"></i>
                                    </th>
                                    <th class="toggalContact">Contact<i class="fa fa-sort ml-1 theme-color" id="sortContact"
                                            aria-hidden="true"></i></th>
                                    <th>Driver Remarks<i class="fa fa-sort ml-1 theme-color" id="sortRemarks"
                                            aria-hidden="true"></i></th>
                                    <th>Driver<i class="fa fa-sort ml-1 theme-color" id="sortDriver" aria-hidden="true"></i>
                                    </th>
                                    <th>Vehicle<i class="fa fa-sort ml-1 theme-color" id="sortVehicle"
                                            aria-hidden="true"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.driver-schedule.partials.driver-schedule-listing')
                            </tbody>
                        </table>
                    </div>
                    <div class="" id="sendDriverScheduleDiv">
                        @if (!$driversBooking->isEmpty())
                            <div class="text-center">
                                <button type="button" id="sendDriverScheduleButton" class="btn btn-dark mt-4"
                                    title="Send Driver Schedule">
                                    <i class="fa fa-paper-plane"></i> Send Driver Schedule
                                </button>
                            </div>
                        @endIf
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    {{ $driversBooking->links('pagination::bootstrap-5') }}
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->
    </div>
    @vite(['resources/js/DriverSchedule.js'])
    <script>
        const props = {
            routes: {
                filterDriversBookings: "{{ route('filter-drivers-bookings') }}",
                exportData: "{{ route('export') }}",
                sendDriverSchedule: "{{ route('send-driver-schedule') }}",
            },
            userTypeId: @json($user->user_type_id)
        }
    </script>
@endsection
