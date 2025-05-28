@extends('components.layout')
@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                        <form id="createVehicleForm" method="post" action="{{ route('save-vehicle') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <a class="" href="{{ route('vehicle-class') }}">
                                            <i class="fa fa-arrow-left" aria-hidden="true"></i>Vehicle
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <h6>View Vehicle Class</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <h3>View Information</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Vehicle Name</div>
                                    <div class="col-md-6">{{ $vehicle_class->name }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Seating Capacity</div>
                                    <div class="col-md-6">{{ $vehicle_class->seating_capacity }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Total Luggage</div>
                                    <div class="col-md-6">{{ $vehicle_class->total_luggage }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Total Pax</div>
                                    <div class="col-md-6">{{ $vehicle_class->total_pax }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Status</div>
                                    <div class="col-md-6">{{ $vehicle_class->status }}</div>
                                </div>
                            </div>
                        </div>
                        </form>
                </div>
            </div>
    </section>
</div>
@vite(['resources/js/Vehicle.js'])
<script>
    const props = {};
</script>
@endsection
