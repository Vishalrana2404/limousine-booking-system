@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Drivers</h1>
                        <p class="normal text-xs">Driver Management</p>
                    </div>
                    <div class="col-sm-9">
                        <div class="row justify-content-end">
                            <div class="col-md-3">
                                <input type="text" id="search" name="search" class="form-control"
                                    placeholder="Search">
                            </div>
                            <div class="col-md-3">
                                <select id="bulkAction" class="form-control form-select custom-select">
                                    <option>Bulk Action</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <a class="w-100 inner-theme-btn" href="{{ route('create-driver') }}" title="Add Driver">
                                        <i class="fa fa-solid fa-plus"></i> Add Driver</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content bg-white">
            <!-- jquery validation -->
            <div class="card">
                <div class="card-header border-0">
                    <!-- <h1 class="card-title medium head-sm">Drivers</h1> -->
                    <!-- <div class="card-tools">
                            <span class="icon setting-icon"></span>
                        </div> -->
                </div>
                <input type="hidden" id="sortOrder">
                <input type="hidden" id="sortColumn">
                <input type="hidden" id="currentPage">
                <!-- /.card-header -->
                <div id="dyanmicHtml">
                    @include('admin.drivers.partials.drivers-listing')
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->
    </div>
    @vite(['resources/js/Drivers.js'])
    <script>
        const props = {
            routes: {
                deleteDrivers: "{{ route('delete-drivers') }}",
                filterDrivers: "{{ route('filter-drivers') }}",
            },
        }
    </script>
@endsection
