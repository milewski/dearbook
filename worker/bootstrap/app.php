<?php

declare(strict_types = 1);

use App\Jobs\FetchAndGenerateJob;
use App\Jobs\ProcessOllamaQueries;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withExceptions(function (Exceptions $exceptions) {

    })
    ->withSchedule(function (Schedule $schedule) {

        if (config('app.mode') === 'ollama') {

            $schedule->job(ProcessOllamaQueries::class)
                ->withoutOverlapping()
                ->everyTenSeconds();

        }

        if (config('app.mode') === 'comfyui') {
            $schedule->job(new FetchAndGenerateJob())->withoutOverlapping()->everyTenSeconds();
        }

    })
    ->create();
