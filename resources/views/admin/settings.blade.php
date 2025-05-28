@extends('components.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header border-bottom">
        <div class="container-fluid">
            <div class="row align-items-center g-3">
                <div class="col-sm-12">
                    <h1 class="semibold head-sm">Account Settings</h1>
                    <p class="text-xs normal">Manage Your Account</p>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content bg-white p-0">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- jquery validation -->
                    <div class="card no-box-shadow">
                        {{-- <div class="card-header">
                            <h5>Manage Your Account</h5>
                        </div> --}}
                        <!-- /.card-header -->
                        <div class="card-body border-0 p-0">
                            <div class="row">
                                <div class="col-2 border-right p-4 custom-nav">
                                    <!-- Tab navs -->
                                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                        <button class="nav-link theme-color text-sm semibold active" data-tab="v-pills-link1" title="User Profile">User Profile</button>
                                        <button class="nav-link theme-color text-sm semibold" data-tab="v-pills-link2" title="Change Password">Change Password</button>
                                    </div>
                                    <!-- Tab navs -->
                                </div>

                                <div class="col-10">
                                    <!-- Tab content -->
                                    <div class="tab-content" id="v-pills-tabContent">
                                        <div class="tab-pane fade show active" id="v-pills-link1">
                                            @include('admin.partials.user-profile')
                                        </div>
                                        <div class="tab-pane fade" id="v-pills-link2">
                                            @include('admin.partials.change-password')
                                        </div>
                                    </div>
                                    <!-- Tab content -->
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->

                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@vite(['resources/js/Settings.js'])
<script>
    const props = {
        routes: {
            changeCurrentPassword: "{{ route('change-current-password') }}",
            updateProfile: "{{ route('update-profile') }}",
            changeProfileImage: "{{ route('change-profile-image') }}",
            removeProfileImage: "{{ route('remove-profile-image') }}",
        },
        images: {
            defaultProfileImage: "{{ asset('/images/profile.svg') }}"
        }
    };
</script>
@endsection