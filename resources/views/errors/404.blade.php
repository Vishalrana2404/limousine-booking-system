<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 Not Found</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('images/favicon.ico')}}">
    @vite(['resources/sass/app.scss','resources/js/app.js','resources/js/bootstrap.bundle.min.js'])
</head>
<body>
    <div class="content-wrapper d-flex justify-content-center align-items-center ms-auto" style="min-height: 100vh;">
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-warning"> 404</h2>
                <div class="error-content ps-4">
                    <h3 class="mb-3"><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>
                    <p class="text-md">
                        We could not find the page you were looking for. Meanwhile, you may <a href="{{ route('dashboard') }}">return to dashboard</a>.
                    </p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>