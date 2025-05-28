@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Corporates</h1>
                        <p class="normal text-xs">Corporate Management</p>
                    </div>
                    <div class="col-sm-9">
                        <div class="row justify-content-end">
                            <div class="col-md-3">
                                <input type="text" id="search" name="search" class="form-control"
                                    placeholder="Search">
                            </div>
                            <div class="col-md-3">
                                <select id="bulkAction" class="form-control form-select custom-select">
                                    <option value="" selected="" disabled="">Bulk Action</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <a class="w-100 inner-theme-btn" href="{{ route('create-hotel') }}"
                                        title="Add Corporate">
                                        <i class="fa fa-solid fa-plus"></i> Add Corporate</a>
                                </div>
                            </div>
                        </div>
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
                <input type="hidden" id="sortColumn">
                <input type="hidden" id="currentPage">
                <div id="dyanmicHtml">
                    @include('admin.hotels.partials.hotel-listing')
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->
    </div>
    @vite(['resources/js/Hotels.js'])
    <script>
        const props = {
            routes: {
                deleteHotels: "{{ route('delete-hotels') }}",
                filterHotels: "{{ route('filter-hotels') }}",
                upadateBulkStatus: "{{ route('update-bulk-hotel-status') }}",
            },
        }
    </script>
@endsection
