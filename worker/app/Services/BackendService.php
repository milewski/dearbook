<?php

declare(strict_types = 1);

namespace App\Services;

use App\Data\AssetsWork;
use App\Services\Traits\Resolvable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

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
    public function updateAssets(AssetsWork $work, Collection $assets): void
    {
        $this->request()->post("/work/$work->id/assets", [
            'assets' => $assets->toArray(),
        ]);
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
