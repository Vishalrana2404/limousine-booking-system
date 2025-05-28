@extends('components.layout')
@section('content')
<form id="updateVehicleForm" method="post" enctype="multipart/form-data" action="{{ route('update-vehicle',$vehicle) }}">
    @csrf
    <div class="content-wrapper">
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <input type="hidden" id="vehicleId" value="{{$vehicle->id}}">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <a class="dark-color back-btn" href="{{ route('vehicles') }}" title="Vehicles">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <rect x="25" y="25" width="25" height="25" transform="rotate(-180 25 25)" fill="url(#pattern0_31_250)" />
                                <defs>
                                    <pattern id="pattern0_31_250" patternContentUnits="objectBoundingBox" width="1" height="1">
                                        <use xlink:href="#image0_31_250" transform="scale(0.0078125)" />
                                    </pattern>
                                    <image id="image0_31_250" width="128" height="128" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJwSURBVHic7d09axRRGIbhe+aAla3B0r8hYmcjGFJbib1so+IXaBtN/GhUrPwW7ESsxcJC1F9gof4CESIKIo7FoASzS3Zmc+bN5twXnDLDA+/DJpmdOQckSZIkSZIkSZIkSZIkSZIkSZIkSZIkSZIkbVABx4G3wA/gK/ACOBAZSsOogUdAM2b9AkZx0TSEEeOHv36dDEun7D6xeQEa4FRUQOWzh+mG/3edjompXLoWoAHOhCRVNtP+Cli/zuYKk3JdWBP9Bg53/JlDwE/g9dbH0dBq4CndPwUa4HxAXmWQgMf0K8HFgLzKYJYSXArIqwwSk+8KWoJCJOAh/UqwHJBXGVgCWQK1JXhAvxJcDsirDBJwn34luBKQVxlYApGAe/QrwcrwcZVDTf8SrA4fVznUwF0sQdEq4Db9SnA1IK8ysASiAm7RrwTXAvIqgwq4Sb8SXA/IqwxmLUE17qJLwCvgW88Lu+Zn3eC/Eixvg1CuYdcKtC1YAp6hEi0m4A6wLziIYuytgDVgd3QShViroxMoVFMD76NTKMy7BHwBjkYnUYhRAj4Au4CDwWE0rFXam0n/HAFe0v5RGP0/qivv2nAjSPPF7wMK5jeCBZvlmQCHP+d8IKRgPhdYMJ8MLpjvBhTMt4MK5vAL5hvCBZtljwCHP+fcIKJgDr9g7hRWMPcKLJi7hRYsAU9w+EVys+jCTXNm0Lh1LiKstt5nug8/24ERGtYC3Yd/ISSpsvDQKPGR6YbvsXE71Ak2H74HR+5gFZPv+3t0bCEq4BjwBvhO+37mc2B/ZChJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRtQ38A9GwPyk+0wKwAAAAASUVORK5CYII=" />
                                </defs>
                            </svg>
                            Vehicles
                        </a>
                        <h6 class="head-sm medium">Edit Vehicle</h6>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="updateVehicleFormButton" class="btn btn-outline-primary float-right mx-2" title="Save">Save</button>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card no-box-shadow">
                <div class="card-header px-0">
                    <div class="row">
                        <h3 class="head-sm medium">Vehicle Information</h3>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="vehicle_class">Vehicle Class <span class="text-danger">*</span></label>
                                <select name="vehicle_class" id="vehicle_class" class="form-control form-select custom-select @error('vehicle_class') is-invalid @enderror" autofocus>
                                    <option value="">Select one</option>
                                    @foreach($vehicleClassData as $vehicleClass)
                                    @if ($vehicle->vehicle_class_id === $vehicleClass->id)
                                    <option value="{{  $vehicleClass->id }}" selected>{{ $vehicleClass->name }}</option>
                                    @else
                                    <option value="{{ $vehicleClass->id }}">{{ $vehicleClass->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('vehicle_class')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="vehicle_number">Vehicle Number <span class="text-danger">*</span></label>
                                <input type="text" id="vehicle_number" name="vehicle_number" value="{{ $vehicle->vehicle_number }}" class="form-control @error('vehicle_number') is-invalid @enderror" placeholder="Vehicle Number" autofocus>
                                @error('vehicle_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="brand">Brand </label>
                                <input type="text" id="brand" name="brand" value="{{ $vehicle->brand }}" class="form-control @error('brand') is-invalid @enderror" placeholder="Brand" autofocus>
                                @error('brand')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" id="model" name="model" value="{{$vehicle->model}}" class="form-control @error('model') is-invalid @enderror" placeholder="Model" autofocus>
                                @error('model')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="image">Image</label>
                                @php
                                $imageUrl = isset($vehicle->image) ? $vehicle->image : '';
                                @endphp
                                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" placeholder="Image" accept="image/*" autofocus>
                                @if(isset($imageUrl))
                                    <img src="{{ Storage::url($imageUrl) }}" alt="" class="mt-3 img-fluid">
                                @endif
                                @error('image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control form-select custom-select @error('status') is-invalid @enderror">
                                    <option value="ACTIVE" {{ $vehicle->status === "ACTIVE" ? 'selected' : '' }}>Active</option>
                                    <option value="INACTIVE" {{ $vehicle->status === "INACTIVE" ? 'selected' : '' }}>Inactive</option>

                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>
@vite(['resources/js/Vehicle.js'])
<script>
    const props = {
        routes: {
            checkUniqueVehicleNumber: "{{route('check-unique-vehicle-number')}}",
        },
    };
</script>
@endsection