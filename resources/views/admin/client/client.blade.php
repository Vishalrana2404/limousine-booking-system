@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Clients</h1>
                        <p class="normal text-xs">Client Management</p>
                    </div>
                    <div class="col-sm-9">
                        <div class="row justify-content-end g-3">
                            <div class="col-md-2">
                                <input type="text" id="search" name="search" class="form-control"
                                    placeholder="Search">
                            </div>
                            <div class="col-md-2">
                                <select id="bulkAction" class="form-control form-select custom-select">
                                    <option>Bulk Action</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterByUserType" class="form-control form-select custom-select">
                                    <option value="">Filter Client Type</option>
                                    @foreach ($userTypeData as $key => $userType)
                                        <option value="{{ $userType->id }}">{{ $userType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterByClient" class="form-control form-select custom-select">
                                    <option value="">Filter Client</option>
                                    @foreach ($hotels as $hotel)
                                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <a class="w-100 inner-theme-btn" href="{{ route('client-create') }}" title="Add User">
                                        <i class="fa fa-solid fa-plus"></i> Add Client</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- jquery validation -->
            <div class="card">
                <div class="card-header border-0">
                    <!-- <h1 class="card-title medium head-sm">Clients</h1> -->
                    <!-- <div class="card-tools">
                                <span class="icon setting-icon"></span>
                            </div> -->
                </div>
                <input type="hidden" id="sortOrder">
                <input type="hidden" id="sortColumn">
                <input type="hidden" id="currentPage">
                <div id="dyanmicHtml">
                    @include('admin.client.partials.clients-listing')
                </div>
            </div>
        </section>
    </div>
    @vite(['resources/js/Clients.js'])
    <script>
        const props = {
            routes: {
                filterClient: "{{ route('filter-clients') }}",
                upadateBulkStatus: "{{ route('update-bulk-client-status') }}",
                deleteClient: "{{ route('delete-client') }}"
            },
        }
    </script>
@endsection
