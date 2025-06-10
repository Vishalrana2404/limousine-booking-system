<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="validation-messages" content="{{ json_encode(__('validation.custom')) }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link rel="shortcut icon" type="image/x-icon" href="{{asset('images/favicon.ico')}}">

    <!-- Choices CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" /> 

    <!-- Jquery UI CSS -->
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">   
    
    <!-- Jquery Js -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    
    <!-- Jquery UI JS -->
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>

    <!-- Choices JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    @vite(['resources/sass/app.scss','resources/js/app.js','resources/js/bootstrap.bundle.min.js'])
    
    <!-- CKEditor CSS -->
    <link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script>  

</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div id="loader">
        <div class="loader-icon"></div>
    </div>
    <div class="wrapper">
        @include('components.nav')
        @include('components.sidebar')
        @include('modals.delete-confirm-modal')
        @include('modals.cancel-booking-modal')
        @yield('content')
        @include('components.footer')
    </div>


    <script>
        window.AUTH_ID = "{{ (Auth::check()) ? Auth::id() : null}}";
        window.filterNotification = "{{route('filter-notification')}}";
        window.markAsRead = "{{route('markAsRead')}}";
        window.notifications = "{{ route('notifications') }}";
    </script>
</body>
</html>

this is my layout file and i am still getting same issue