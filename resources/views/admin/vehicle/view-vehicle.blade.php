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
                                        <a class="" href="{{ route('vehicles') }}">
                                            <i class="fa fa-arrow-left" aria-hidden="true"></i>Vehicle
                                        </a>
                                    </div>
                                <div class="row">
                                    <h6>View Vehicle</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <h3>View Information</h3>
                                </div>
                            </div>
                            @php
                                $imageUrl = isset($vehicle->image) ? $vehicle->image : '';
                            @endphp
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Vehicle Image</div>
                                    <div class="col-md-6">
                                        @if(isset($imageUrl))
                                        <img src="{{ Storage::url($imageUrl) }}" alt="Uploaded Image" style="max-width: 200px;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Vehicle Class</div>
                                    <div class="col-md-6">{{ $vehicle->vehicleClass->name }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Vehicle Number</div>
                                    <div class="col-md-6">{{ $vehicle->vehicle_number }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Brand</div>
                                    <div class="col-md-6">{{ $vehicle->brand }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Model</div>
                                    <div class="col-md-6">{{ $vehicle->model }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">Status</div>
                                    <div class="col-md-6">{{ $vehicle->status }}</div>
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
