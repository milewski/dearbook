<?php

declare(strict_types = 1);

use App\Jobs\ProcessComfyUIQueries;
use App\Jobs\ProcessOllamaQueries;
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

        if (config('app.mode') === 'ollama') {
            $schedule->job(ProcessOllamaQueries::class)->everySecond();
        }

        if (config('app.mode') === 'comfyui') {
            $schedule->job(ProcessComfyUIQueries::class)->everyTenSeconds();
        }

    })
    ->create();
