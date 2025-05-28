@extends('components.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header border-bottom">
    <div class="container-fluid">
      <div class="mb-2 d-flex align-items-center justify-content-between">
        <div class="col-sm-3">
          <h1 class="head-sm medium">Dashboard</h1>
          <p class="text-xs normal">Business Overview & Administrative Dashboard</p>
        </div>
        
        <div class="col-sm-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                </div>
                <input type="text" id="pickupDateBooking" class="form-control" placeholder="Select date"
                    autocomplete="off" autofocus />
            </div>
        </div>
        <div class="col-sm-3">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header text-center">
              <h4 class="">Types Of Bookings %</h4>
            </div>
            <div class="card-body text-center">
              <canvas id="typesOfBookingChart" width="300" height="300"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-header text-center">
              <h4 class="">No Of Bookings</h4>
            </div>
            <div class="card-body text-center">
              <canvas id="lineChartForNoOfBookings" width="300" height="300"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header text-center">
              <h4 class="">Total Bookings Vs Cancellations</h4>
            </div>
            <div class="card-body text-center">
              <canvas id="cancellationBookingsChart" width="300" height="300"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-header text-center">
              <h4 class="">No Of Cancellation</h4>
            </div>
            <div class="card-body text-center">
              <canvas id="lineChartForNoOfCancellation" width="300" height="300"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

@vite(['resources/js/Dashboard.js'])
<script>
    const props = {      
      routes: {
                filterDashboardData: "{{ route('filter-dashboard-data') }}",
            },
      finalData: @json($finalData),
    }
</script>
@endsection
