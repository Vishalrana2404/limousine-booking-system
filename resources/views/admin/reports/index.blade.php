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
                <div class="row align-items-center g-3">
                    <div class="col-sm-2">
                        <h1 class="semibold head-sm">Reports</h1>
                        <!-- <p class="normal text-xs">Driver's Schedule</p> -->
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-2">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"
                            title="Hide Contact">
                            <input type="checkbox" class="custom-control-input" id="hideContact" checked="">
                            <label class="custom-control-label pb-0" for="hideContact">Hide/Show Contact</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"
                            title="Hide Pickup">
                            <input type="checkbox" class="custom-control-input" id="hidePickup" checked="">
                            <label class="custom-control-label pb-0" for="hidePickup">Hide/Show Pickup</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"
                            title="Hide DropOff">
                            <input type="checkbox" class="custom-control-input" id="hideDropOff" checked="">
                            <label class="custom-control-label pb-0" for="hideDropOff">Hide/Show DropOff</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"
                            title="Hide Additional Stops">
                            <input type="checkbox" class="custom-control-input" id="hideAdditionalStops" checked="">
                            <label class="custom-control-label pb-0" for="hideAdditionalStops">Hide/Show Additional Stops</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"
                            title="Hide Guest">
                            <input type="checkbox" class="custom-control-input" id="hideGuest" checked="">
                            <label class="custom-control-label pb-0" for="hideGuest">Hide/Show Guest</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"
                            title="Hide Event">
                            <input type="checkbox" class="custom-control-input" id="hideEvent" checked="">
                            <label class="custom-control-label pb-0" for="hideEvent">Hide/Show Event</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2">
                <div class="row align-items-center g-3">                    
                    <div class="col-md-2">
                        <input type="text" id="search_by_booking_id" name="search_by_booking_id" class="form-control" placeholder="Search By Booking Id">
                    </div>                    
                    <div class="col-md-2">
                        <input type="text" id="search" name="search" class="form-control" placeholder="Search">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                            </div>
                            <input type="text" id="pickupDate" class="form-control" placeholder="Select date"
                                autocomplete="off" autofocus />
                        </div>
                    </div>
                    <div class="col-md-4" id="exportOptions">
                        @if (!$driversBooking->isEmpty())
                            <select id="exportFormat" class="form-control form-select custom-select">
                                <option value="">Export</option>
                                <option value="excel">Excel</option>
                            </select>
                        @endIf
                    </div>  
                </div>
                <div class="container-fluid mt-2">
                    <div class="row align-items-center g-3">  
                        <div class="col-md-2">
                            <select id="driversList" class="form-control form-select custom-select">
                                <option value="">Select Driver</option>
                                @foreach ($driverData as $drivers)
                                    <option value="{{ $drivers->id }}">{{ $drivers->name }}</option>
                                @endforeach
                            </select>
                        </div> 
                        <div class="col-md-2">
                            <select id="driverTypeList" class="form-control form-select custom-select">
                                <option value="">Select Driver Type</option>
                                <option value="INHOUSE">In-House</option>
                                <option value="OUTSOURCE">Out-Source</option>
                            </select>
                        </div>     
                        <div class="col-md-3">
                            <select id="hotelsList" class="form-control form-select custom-select">
                                <option value="">Select Corporate</option>
                                @foreach ($hotelsData as $hotel)
                                    <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="usersList" class="form-control form-select custom-select">
                                <option value="">Select Booked By</option>
                                @foreach ($usersData as $user)
                                    @if(!empty($user->first_name))
                                        <option value="{{ $user->id }}">{{ $user->first_name . ' ' . $user->last_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="eventsList" class="form-control form-select custom-select">
                                <option value="">Select Event</option>
                                @foreach ($eventsData as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div> 
                    </div>
                </div>
            </div>
        </section>
        <section class="content bg-white">
            <div class="card">
                <div class="card-header border-0">
                </div>
                <input type="hidden" id="sortOrder">
                <div class="card-body p-0">
                    <div class="table-responsive custom-table table-scroll-height">
                        <table id="reportsTable" class="table table-head-fixed text-nowrap table-hover m-0">
                            <thead>
                                <tr>
                                    <th>Booking <i class="fa fa-sort ml-1 theme-color" id="sortBooking"
                                            aria-hidden="true"></i></th>
                                    <th>Time<i class="fa fa-sort ml-1 theme-color" id="sortTime" aria-hidden="true"></i>
                                    </th>
                                    <th>Type<i class="fa fa-sort ml-1 theme-color" id="sortType" aria-hidden="true"></i>
                                    </th>
                                    <th class="toggalPickup">Pick-up<i class="fa fa-sort ml-1 theme-color" id="sortPickUp"
                                            aria-hidden="true"></i></th>
                                    <th class="toggalDropOff">Drop-off<i class="fa fa-sort ml-1 theme-color" id="sortDropOff"
                                            aria-hidden="true"></i></th>
                                    <th class="toggalAdditionalStops">Additional Stops<i class="fa fa-sort ml-1 theme-color" id="sortAdditionalStops"
                                            aria-hidden="true"></i></th>
                                    <th class="toggalGuest">Guest Name<i class="fa fa-sort ml-1 theme-color" id="sortGuestName"
                                            aria-hidden="true"></i></th>
                                    @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                                    <th>Corporate<i class="fa fa-sort ml-1 theme-color" id="sortCorporate" aria-hidden="true"></i>
                                    @endif
                                    </th>
                                    <th class="toggalEvent">Event<i class="fa fa-sort ml-1 theme-color" id="sortEvent" aria-hidden="true"></i>
                                    </th>
                                    <th class="toggalContact">Contact<i class="fa fa-sort ml-1 theme-color" id="sortContact"
                                            aria-hidden="true"></i></th>
                                    <th>Driver<i class="fa fa-sort ml-1 theme-color" id="sortDriver" aria-hidden="true"></i>
                                    </th>
                                    <th>Vehicle<i class="fa fa-sort ml-1 theme-color" id="sortVehicle"
                                            aria-hidden="true"></i></th>
                                    <th>Status<i class="fa fa-sort ml-1 theme-color" id="sortStatus"
                                            aria-hidden="true"></i></th>
                                    <th>Booked By<i class="fa fa-sort ml-1 theme-color" id="sortBookedBy"
                                            aria-hidden="true"></i></th>
                                    <th>Access Given Clients<i class="fa fa-sort ml-1 theme-color" id="sortAccessGivenClients"
                                            aria-hidden="true"></i></th>
                                    <th>Booking Date<i class="fa fa-sort ml-1 theme-color" id="sortBookingDate"
                                            aria-hidden="true"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.reports.partials.reports-listing')
                            </tbody>
                        </table>
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
    @vite(['resources/js/Reports.js'])
    <script>
        const props = {
            routes: {
                filterReports: "{{ route('filter-reports') }}",
                exportData: "{{ route('export-reports') }}",
                clientsOfCorporates: "{{ route('get-clients-by-corporate-id') }}"
            },
            userTypeId: @json($user->user_type_id)
        }
    </script>
@endsection
