<?php

declare(strict_types = 1);

namespace App\Services;

use App\Data\BookPayload;
use App\Data\Work;
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
    public function getWork(): ?Work
    {
        $response = $this->request()->get('/work')->json();

        if (blank($response)) {
            return null;
        }

        return Work::from($response);
    }

    /**
     * @throws ConnectionException
     */
    public function finishWork(Work $work, BookPayload $payload, Collection $assets): void
    {
        $body = [
            [ 'name' => 'id', 'contents' => $work->id ],
            [ 'name' => 'title', 'contents' => $payload->data->title ],
            [ 'name' => 'synopsis', 'contents' => $payload->data->synopsis ],
        ];

        foreach ($payload->data->paragraphs as $index => $paragraph) {

            $body[] = [
                'name' => "paragraphs[$index]",
                'contents' => $paragraph,
            ];

        }

        foreach ($assets as $name => $path) {

            $body[] = [
                'name' => $name,
                'contents' => Storage::disk('public')->readStream($path),
            ];

        }

        $this->request()->asMultipart()->post('/work/finish', $body);

    }

    private function request(): PendingRequest
    {
        return Http::timeout(60 * 5)
            ->baseUrl(config('app.backend.url'))
            ->withHeader('x-api-key', config('app.backend.worker_api_key'))
            ->acceptJson();
    }
}
