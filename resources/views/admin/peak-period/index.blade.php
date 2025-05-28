@extends('components.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header border-bottom">
        <div class="container-fluid">
            <div class="row align-items-center g-3">
                <div class="col-sm-3">
                    <h1 class="semibold head-sm">Peak Period</h1>
                    <p class="normal text-xs">Peak Period Management</p>
                </div>
                <div class="col-sm-9">
                    <div class="row justify-content-end g-3">
                        <div class="col-md-3">
                            <select id="bulkAction" class="form-control form-select custom-select">
                                <option value="" selected="" disabled="">Bulk Action</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="search" name="search" class="form-control" placeholder="Search">
                        </div>
                        <div class="col-md-3">
                            <div class="">
                                <a class="w-100 inner-theme-btn" id="addPeakPeriod" href="{{ route('add-peak-period') }}" title="Peak Period">
                                    <i class="fa fa-solid fa-plus"></i> Add Peak Period
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- jquery validation -->
                    <div class="card">
                        <div class="card-header border-0">
                            {{-- <h1 class="card-title medium head-sm">Peak Period</h1> --}}
                            <!-- <div class="card-tools">
                                <span class="icon setting-icon"></span>
                            </div> -->
                        </div>
                        <input type="hidden" id="sortOrder">
                        <!-- /.card-header -->
                        <div class="card-body p-0">
                            <div class="table-responsive custom-table">
                                <table id="peakPeriodTable" class="table table-head-fixed text-nowrap table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input peakPeriodTableCheckbox" type="checkbox" id="bulkPeakPeriodAction">
                                                    <label for="bulkPeakPeriodAction" class="custom-control-label"></label>
                                                </div>
                                            </th> 
                                            <th>Event <i class="fa fa-sort ml-1 text-dark" id="sortEvent" aria-hidden="true"></i></th>
                                            <th>Start Date<i class="fa fa-sort ml-1 text-dark" id="sortStartDate" aria-hidden="true"></i></th>
                                            <th>End Date<i class="fa fa-sort ml-1 text-dark" id="sortEndDate" aria-hidden="true"></i></th>
                                            <th>Status<i class="fa fa-sort ml-1 text-dark" id="sortStatus" aria-hidden="true"></i></th>
                                            <th style="width: 40px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @include('admin.peak-period.partials.peak-period')
                                    </tbody>
                                </table>
                             </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer clearfix">
                            {{$peakPeriodData->links('pagination::bootstrap-5')}}
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@vite(['resources/js/PeakPeriod.js'])
<script>
    const props = {
        routes: {
            filterPeakPeriod: "{{route('filter-peak-period')}}",
            updateBulkStatus: "{{route('update-bulk-peak-period-status')}}",
            deletePeakPeriod: "{{route('delete-peak-period')}}"
        },
    };
</script>
@endsection