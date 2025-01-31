<?php

use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isCaraka;
use App\Http\Middleware\isReviewer;
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
            'isAdmin' => App\Http\Middleware\isAdmin::class,
            'isReviewer' => App\Http\Middleware\isReviewer::class,
            'isCaraka' => App\Http\Middleware\isCaraka::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
