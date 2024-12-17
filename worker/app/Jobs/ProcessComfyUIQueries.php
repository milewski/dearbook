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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Throwable;

class ProcessComfyUIQueries implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * @throws ConnectionException
     */
    public function handle(): void
    {
        if ($work = BackendService::resolve()->getAssetsWork()) {

            try {

                $workflowId = ComfyUIService::resolve()->execute('main.workflow.json', $work);

                $assets = value(function () use ($workflowId) {

                    while (true) {

                        $response = ComfyUIService::resolve()->fetchOutputs($workflowId);

                        if ($response instanceof Collection) {
                            return $response;
                        }

                        if ($response === false) {
                            return false;
                        }

                        sleep(5);

                    }

                });

                if ($assets === false) {
                    throw new Exception('Failed to generate images...');
                }

                foreach ($assets as $_ => $asset) {

                    ImageOptimizer::optimize(
                        Storage::disk('public')->path($asset),
                    );

                }

                BackendService::resolve()->uploadAssets($work, $assets);

            } catch (Throwable $error) {

                BackendService::resolve()->reportFailure($work->id, $error->getMessage());

            }

        }
    }
}
