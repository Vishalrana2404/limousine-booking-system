@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Notifications</h1>
                        <p class="normal text-xs">View all notifications</p>
                    </div>

                    <div class="col-sm-5">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                            </div>
                            <input type="text" id="notificationDateRange" class="form-control" placeholder="Select date"
                                autocomplete="off" autofocus />
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content bg-white">
            <!-- jquery validation -->
            <div class="card">
                <input type="hidden" id="sortOrder">
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table id="notificationTable" class="table table-head-fixed text-nowrap table-hover m-0">
                            <thead>
                                <tr>
                                    <th>Notification</th>
                                    <th>Date<i class="fa fa-sort ml-1 text-dark" id="sortDate"
                                            aria-hidden="true"></i></th>
                                    <th>Time<i class="fa fa-sort ml-1 text-dark" id="sortTime"
                                            aria-hidden="true"></i></th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('components.partials.notification-listing')
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </section>
    </div>
    @vite(['resources/js/NotificationList.js'])
    <script>
        const props = {};
    </script>
@endsection
