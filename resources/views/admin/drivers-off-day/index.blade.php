@extends('components.layout')
@section('content')
<form id="dayOffForm" method="post" action="{{ route('save-driver-off-days') }}">
    @csrf
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header mb-4 border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-9">
                        <h1 class="head-sm medium">Drivers</h1>
                        <p class="normal text-xs">Inhouse Driver Off Days</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="card">
                <div class="card-header border-0">
                       <div class="row justify-content-end">
                            <div class="col-md-2">
                                <button type="button" class="w-100 inner-theme-btn weekButton" id="pastWeek" title="Past Week"><i class="fa fa-solid fa-arrow-left mr-1"></i> Past Week</button>
                                <button type="button" class="w-100 inner-theme-btn monthButton" id="pastMonth" style="display: none;" title="Past Month"><i class="fa fa-solid fa-arrow-left mr-1"></i>Past Month</button>
                            </div>
                            <div class="col-md-3 text-md-center">
                                <select class="form-control form-select custom-select" id="timeRangeSelect">
                                    <option value="WEEK">Week</option>
                                    <option value="MONTH">Month</option>
                                </select>
                            </div>
                            <div class="col-md-2 text-md-right">
                                <button type="button" class="w-100 inner-theme-btn weekButton" id="nextWeek" title="Next Week">Next Week <i class="fa fa-solid fa-arrow-right ml-1"></i></button>
                                <button type="button" class="w-100 inner-theme-btn monthButton" id="nextMonth" style="display: none;" title="Next Month">Next Month <i class="fa fa-solid fa-arrow-right ml-1"></i></button>
                            </div>
                       </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table id="driverOffDayTable" class="table driverOffDayTable table-bordered table-head-fixed text-nowrap table-hover m-0">
                            <thead>
                                <tr>
                                    <!-- Dates will be inserted dynamically here -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be inserted dynamically here -->
                            </tbody>
                        </table>
                    </div>
                </div>
        </section>
    </div>
</form>
@vite(['resources/js/DriverOffDay.js'])
<script>
    const props = {
        routes: {
            saveOffDays: "{{route('save-driver-off-days')}}",
            getDriverOffDays: "{{route('get-driver-off-days')}}",
        },
        driverData: @json($driverData),
    }
</script>
@endsection