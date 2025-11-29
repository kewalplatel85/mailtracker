<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'company.scope' => \App\Http\Middleware\CompanyScope::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        // Add company scope to web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\CompanyScope::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
