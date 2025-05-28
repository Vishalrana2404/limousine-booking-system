<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="validation-messages" content="{{ json_encode(__('validation.custom')) }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('images/favicon.ico')}}">

    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/bootstrap.bundle.min.js', 'resources/js/Login.js'])
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo head-sm">
            <a class="dark-color" href="#"><b>{{ config('app.name', 'Laravel') }}</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            @yield('content')
        </div>
    </div>
    <!-- /.login-box -->
    <script>
        const props = {};
    </script>
</body>

</html>
