<?php

use App\Console\Commands\FetchAndStoreResigned;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use App\Http\Middleware\RedirectIfAuthenticatedFor2FA;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'redirect.if.authenticated.2fa' => RedirectIfAuthenticatedFor2FA::class,
        ]);
    })


    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
