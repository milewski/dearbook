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

                    retry(
                        times: 5,
                        callback: function () use ($workflowId) {

                            $response = ComfyUIService::resolve()->fetchOutputs($workflowId);

                            if ($response instanceof Collection) {
                                return $response;
                            }

                            if ($response === false) {
                                return false;
                            }

                            throw new Exception('waiting');

                        },
                        sleepMilliseconds: 5000,
                    );

                });

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
