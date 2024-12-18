<?php

declare(strict_types = 1);

namespace App\Services;

use App\Data\AssetsWork;
use App\Services\Traits\Resolvable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BackendService
{
    use Resolvable;

    /**
     * @throws ConnectionException
     */
    public function reportFailure(string $id, string $message): void
    {
        $this->request()->post("/work/$id/failure", [
            'reason' => $message,
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function getAssetsWork(): ?AssetsWork
    {
        $response = $this->request()->get('/work/assets')->json();

        if (blank($response)) {
            return null;
        }

        return AssetsWork::from($response);
    }

    /**
     * @throws ConnectionException
     */
    public function uploadAssets(AssetsWork $work, Collection $assets): void
    {
        $body = [];

        foreach ($assets as $name => $path) {

            $body[] = [
                'name' => $name,
                'contents' => Storage::disk('public')->readStream($path),
            ];

        }

        $this->request()->asMultipart()->post("/work/$work->id/assets", $body);
    }

    private function request(): PendingRequest
    {
        return Http::timeout(30)
            ->retry(2)
            ->baseUrl(config('app.backend.url'))
            ->withHeader('x-api-key', config('app.backend.worker_api_key'))
            ->throw()
            ->acceptJson();
    }
}
