@extends('components.layout')
@section('content')
<form id="createPeakPeriodForm" method="post" action="{{ route('save-peak-period') }}">
    @csrf
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <a class="dark-color back-btn" href="{{ route('peak-period') }}" title="Peak Period">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <rect x="25" y="25" width="25" height="25" transform="rotate(-180 25 25)" fill="url(#pattern0_31_250)" />
                                <defs>
                                    <pattern id="pattern0_31_250" patternContentUnits="objectBoundingBox" width="1" height="1">
                                        <use xlink:href="#image0_31_250" transform="scale(0.0078125)" />
                                    </pattern>
                                    <image id="image0_31_250" width="128" height="128" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJwSURBVHic7d09axRRGIbhe+aAla3B0r8hYmcjGFJbib1so+IXaBtN/GhUrPwW7ESsxcJC1F9gof4CESIKIo7FoASzS3Zmc+bN5twXnDLDA+/DJpmdOQckSZIkSZIkSZIkSZIkSZIkSZIkSZIkSZIkbVABx4G3wA/gK/ACOBAZSsOogUdAM2b9AkZx0TSEEeOHv36dDEun7D6xeQEa4FRUQOWzh+mG/3edjompXLoWoAHOhCRVNtP+Cli/zuYKk3JdWBP9Bg53/JlDwE/g9dbH0dBq4CndPwUa4HxAXmWQgMf0K8HFgLzKYJYSXArIqwwSk+8KWoJCJOAh/UqwHJBXGVgCWQK1JXhAvxJcDsirDBJwn34luBKQVxlYApGAe/QrwcrwcZVDTf8SrA4fVznUwF0sQdEq4Db9SnA1IK8ysASiAm7RrwTXAvIqgwq4Sb8SXA/IqwxmLUE17qJLwCvgW88Lu+Zn3eC/Eixvg1CuYdcKtC1YAp6hEi0m4A6wLziIYuytgDVgd3QShViroxMoVFMD76NTKMy7BHwBjkYnUYhRAj4Au4CDwWE0rFXam0n/HAFe0v5RGP0/qivv2nAjSPPF7wMK5jeCBZvlmQCHP+d8IKRgPhdYMJ8MLpjvBhTMt4MK5vAL5hvCBZtljwCHP+fcIKJgDr9g7hRWMPcKLJi7hRYsAU9w+EVys+jCTXNm0Lh1LiKstt5nug8/24ERGtYC3Yd/ISSpsvDQKPGR6YbvsXE71Ak2H74HR+5gFZPv+3t0bCEq4BjwBvhO+37mc2B/ZChJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRtQ38A9GwPyk+0wKwAAAAASUVORK5CYII=" />
                                </defs>
                            </svg>
                            Peak Period
                        </a>
                        <h6 class="head-sm medium">Add Peak Period</h6>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="submitPeakPeriodFormButton" class="float-right btn btn-outline-primary mx-2" title="Save">Save</button>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card no-box-shadow">
                            <div class="card-header px-0">
                                <h3 class="head-sm medium">Peak Period Information</h3>
                            </div>
                            <div class="card-body px-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="event">Event <span class="text-danger">*</span></label>
                                            <input type="text" id="event" name="event" value="{{ old('event') }}" class="form-control @error('event') is-invalid @enderror" placeholder="Event" autofocus>
                                            @error('event')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                            <input type="text" id="start_date" name="start_date" value="{{ old('start_date') }}" class="form-control @error('start_date') is-invalid @enderror" placeholder="Start Date" autofocus>
                                            @error('start_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="end_date">End Date <span class="text-danger">*</span></label>
                                            <input type="text" id="end_date" name="end_date" value="{{ old('end_date') }}" class="form-control @error('end_date') is-invalid @enderror" placeholder="End Date" autofocus>
                                            @error('end_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control form-select custom-select @error('status') is-invalid @enderror">
                                                    <option value="ACTIVE" {{ old('status') === "ACTIVE" ? 'selected' : '' }}>Active</option>
                                                    <option value="INACTIVE" {{ old('status') === "INACTIVE" ? 'selected' : '' }}>Inactive</option>
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
                    </div>
                </div>
        </section>
    </div>
</form>
@vite(['resources/js/PeakPeriod.js'])
<script>
    const props = {
        isCreatePage:true
    };
</script>
@endsection