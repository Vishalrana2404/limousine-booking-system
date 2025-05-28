@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Term & Conditions</h1>
                    </div>
                    <div class="col-sm-9">
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center my-5 ml-5">
                    @if (!empty($termConditions))
                        <ul class="text-sm list-bullet">
                            @foreach ($termConditions as $terms)
                                <li class="step-list-item bold">{{ $terms }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>Terms & Conditions Coming Soon.</p>
                    @endif
                </div>
            </div>
        </section>
    </div>
    <script>
        const props = {}
    </script>
@endsection
