<?php

declare(strict_types = 1);

namespace App\Services;

use App\Data\AssetsWork;
use App\Data\Storyline;
use App\Data\StorylineWork;
use App\Services\Traits\Resolvable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BackendService
{
    use Resolvable;

    public function reportFailure(string $id, string $message): void
    {
        $this->request()->post("/work/$id/failure", [
            'reason' => $message,
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function updateBookStoryline(string $id, Storyline $payload): void
    {
        $this->request()->post("/work/$id/storyline", [
            'title' => $payload->data->title,
            'synopsis' => $payload->data->synopsis,
            'paragraphs' => $payload->data->paragraphs,
            'illustrations' => $payload->illustrations,
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function failGeneration(string $id): void
    {
        $this->request()->post('/work/fail', [ 'id' => $id ]);
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
    public function getStorylineWork(): ?StorylineWork
    {
        $response = $this->request()->get('/work/storyline')->json();

        if (blank($response)) {
            return null;
        }

        return StorylineWork::from($response);
    }

    /**
     * @throws ConnectionException
     */
    public function uploadAssets(AssetsWork $work, Collection $assets): void
    {
        $body = [
            [ 'name' => 'id', 'contents' => $work->id ],
        ];

        foreach ($assets as $name => $path) {

            $body[] = [
                'name' => $name,
                'contents' => Storage::disk('public')->readStream($path),
            ];

        }

        $this->request()->asMultipart()->post('/work/assets', $body);
    }

    private function request(): PendingRequest
    {
        return Http::timeout(60 * 5)
            ->baseUrl(config('app.backend.url'))
            ->withHeader('x-api-key', config('app.backend.worker_api_key'))
            ->throw()
            ->acceptJson();
    }
}
