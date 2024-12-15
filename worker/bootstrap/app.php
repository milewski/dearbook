<?php

declare(strict_types = 1);

use App\Jobs\FetchAndGenerateJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {

            Route::middleware('api')
                ->domain(config('app.domains.api'))
                ->group(base_path('routes/api.php'));

        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(new FetchAndGenerateJob())->withoutOverlapping()->everyTenSeconds();
    })
    ->create();
