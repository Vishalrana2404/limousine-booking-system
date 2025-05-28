@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Logs</h1>
                    </div>
                    <div class="col-sm-9">
                        <div class="row justify-content-end g-3">
                            <div class="col-md-3">
                                <div class="">
                                    <div class="input-group">
                                     <div class="input-group-prepend">
                                            <span class="input-group-text">#
                                        </div>
                                        <input type="text" id="searchByBookingId" class="form-control"
                                            placeholder="Search By Booking ID" autocomplete="off" autofocus />                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" id="createdDate" class="form-control"
                                            placeholder="Select date" autocomplete="off" autofocus />
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <select id="usersDropdown" class="form-control form-select custom-select">
                                        <option value="">Select Users</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->first_name }}
                                                {{ $user->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center my-5">
                    <div class="col-md-6" id="timeLine">
                        @include('admin.logs.partials.logs')
                    </div>
                </div>
            </div>
        </section>
    </div>
    @vite(['resources/js/Logs.js'])
    <script>
        const props = {
            routes: {
                filterLogs: "{{ route('filter-logs') }}",
            },
        }
    </script>
@endsection
