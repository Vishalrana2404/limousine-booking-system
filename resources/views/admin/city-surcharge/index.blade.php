@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Outside City Surcharge</h1>
                        <p class="normal text-xs">Outside City Surcharge</p>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div id="map" style="height: 500px;">
                </div>
                <div class="row justify-content-center my-5 ml-5">
                    <ul class="text-sm list-bullet">
                        <li class="step-list-item"><strong>Add :</strong> Click at on place, again click at another place
                            there will be draw a line adjust it according to your need then <strong>right click outside</strong> of the red
                            area it wiil be saved.</li>
                        <li class="step-list-item"><strong>Update :</strong> Increase/Decrease saved area using mouse then
                            <strong>right click inside</strong> the red area it will be updated.</li>
                        <li class="step-list-item"><strong>Remove :</strong> Double click inside red area it will be
                            removed.</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
    @vite(['resources/js/CitySurcharge.js'])
    <script>
        const props = {
            routes: {
                saveCitySurcharge: "{{ route('save-city-surcharge') }}",
                deleteCitySurcharge: "{{ route('delete-city-surcharge') }}",
            },
            savedCities: @json($savedCities),
        };
    </script>
@endsection
