<?php

declare(strict_types = 1);

namespace App\Services;

use App\Services\Traits\Resolvable;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OllamaService
{
    use Resolvable;

    public function pool(Collection $payloads): Collection
    {
        $responses = Http::pool(
            fn (Pool $pool) => $payloads->map(
                fn (array $payload) => $this->generateJsonSchema(...$payload, ...[ $pool ]),
            ),
        );

        return Collection::wrap($responses)->map(
            fn (Response $response) => Collection::make(
                json_decode(
                    json: $response->json('response'),
                    associative: true,
                ),
            ),
        );
    }

    /**
     * @throws ConnectionException
     */
    public function generateJsonSchema(
        string $prompt,
        array $schema,
        null|Pool|PendingRequest $request = null,
    ): Collection|PromiseInterface
    {
        $response = $this->request($request)->post('/generate', [
            'model' => config('app.ollama.model'),
            'prompt' => $prompt,
            'format' => $schema,
            'stream' => false,
        ]);

        if ($request instanceof Pool) {
            return $response;
        }

        return Collection::make(
            json_decode(
                json: $response->json('response'),
                associative: true,
            ),
        );
    }

    private function request(null|Pool|PendingRequest $request = null): PendingRequest
    {
        $request ??= Http::createPendingRequest();

        return $request
            ->timeout(120)
            ->retry(2)
            ->baseUrl(sprintf('%s/api', config('app.ollama.url')))
            ->asJson()
            ->throw();
    }
}
