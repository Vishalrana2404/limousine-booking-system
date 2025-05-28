@extends('components.layout')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Events</h1>
                        <p class="normal text-xs">Events Management</p>
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
                                    @php
                                        $user = Auth::user();
                                        $userTypeSlug = $user->userType->slug ?? null;
                                    @endphp
                                    @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
                                    <option value="delete">Delete</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <a class="w-100 inner-theme-btn" href="{{ route('create-event') }}"
                                        title="Add Event">
                                        <i class="fa fa-solid fa-plus"></i> Add Event</a>
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
                    @include('admin.events.partials.event-listing')
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->
    </div>
    @vite(['resources/js/Events.js'])
    <script>
        const props = {
            routes: {
                deleteEvents: "{{ route('delete-events') }}",
                filterEvents: "{{ route('filter-events') }}",
                filterEventsForClient: "{{ route('filter-events-for-client') }}",
                upadateBulkStatus: "{{ route('update-bulk-event-status') }}",
            },
            loggedUser:@json(Auth::user()),
        }
    </script>
@endsection
