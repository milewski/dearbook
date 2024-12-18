<?php

declare(strict_types = 1);

use App\Jobs\ProcessComfyUIQueries;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withExceptions(function (Exceptions $exceptions) {

    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(ProcessComfyUIQueries::class)->everyTenSeconds();
    })
    ->create();
