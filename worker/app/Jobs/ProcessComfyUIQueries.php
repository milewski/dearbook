<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Services\BackendService;
use App\Services\ComfyUI\ComfyUIService;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class ProcessComfyUIQueries implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * @throws ConnectionException
     */
    public function handle(BackendService $backendService, ComfyUIService $comfyUIService): void
    {
        if ($work = $backendService->getAssetsWork()) {

            try {

                $assets = $comfyUIService->fetchOutputs(
                    $comfyUIService->execute('main.workflow.json', $work),
                );

                if ($assets === false) {
                    throw new Exception('Failed to generate images...');
                }

                $backendService->uploadAssets($work, $assets);

            } catch (Throwable $error) {

                $backendService->reportFailure($work->id, $error->getMessage());

            }

        }
    }
}
