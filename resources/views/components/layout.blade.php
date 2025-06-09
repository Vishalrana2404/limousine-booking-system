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
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <!-- Choices JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
    
    @vite(['resources/sass/app.scss','resources/js/app.js','resources/js/bootstrap.bundle.min.js'])
    
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
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