<?php

namespace App\Jobs;

use App\Services\BackendService;
use App\Services\BookService;
use App\Services\ComfyUI\ComfyUIService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Throwable;

class FetchAndGenerateJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * @return void
     * @throws ConnectionException
     * @throws FilesystemException
     * @throws Throwable
     */
    public function handle(): void
    {
        if ($work = BackendService::resolve()->getWork()) {

            try {

                $data = BookService::resolve()->generateStoryline($work);

                $workflowId = ComfyUIService::resolve()->execute('main.workflow.json', $data);

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

                foreach ($assets as $_ => $asset) {

                    ImageOptimizer::optimize(
                        Storage::disk('public')->path($asset),
                    );

                }

                BackendService::resolve()->finishWork($work, $data, $assets);

            } catch (Throwable) {

                BackendService::resolve()->failGeneration($work->id);

            }
        }
    }
}
