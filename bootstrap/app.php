<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        channels: __DIR__ . '/../routes/channels.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'Auth' => \App\Http\Middleware\AuthenticateUser::class,
            'Admin'   =>  \App\Http\Middleware\Admin::class,
            'Staff'   =>  \App\Http\Middleware\Staff::class,
            'ClientAdmin'   =>  \App\Http\Middleware\ClientAdmin::class,
            'ClientStaff' =>    \App\Http\Middleware\ClientStaff::class,
            'SanitizeInput' => \App\Http\Middleware\SanitizeInput::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
