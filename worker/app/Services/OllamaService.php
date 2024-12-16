<?php

declare(strict_types = 1);

namespace App\Services;

use App\Services\Traits\Resolvable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OllamaService
{
    use Resolvable;

    /**
     * @throws ConnectionException
     */
    public function generateJsonSchema(string $prompt, array $schema): Collection
    {
        $response = $this->request()->post('/generate', [
            'model' => config('app.ollama.model'),
            'prompt' => $prompt,
            'format' => $schema,
            'stream' => false,
        ]);

        return collect(json_decode(
            json: $response->json('response'),
            associative: true,
        ));
    }

    /**
     * @throws ConnectionException
     */
    public function generateJson(string $prompt): Collection
    {
        $response = $this->request()->post('/generate', [
            'model' => config('app.ollama.model'),
            'prompt' => $prompt,
            'format' => 'json',
            'stream' => false,
        ]);

        return collect(json_decode(
            json: $response->json('response'),
            associative: true,
        ));
    }

    /**
     * @throws ConnectionException
     */
    public function generateEmbedding(array $inputs): Collection
    {
        return $this->request()->post('/embed', [
            'model' => config('app.ollama.embedding'),
            'input' => $inputs,
            'stream' => false,
        ])->collect('embeddings');
    }

    /**
     * https://github.com/ollama/ollama/blob/main/docs/api.md#pull-a-model
     *
     * @throws ConnectionException
     */
    public function pullModel(string $model): array
    {
        $response = $this->request()->post('/pull', [
            'name' => $model,
            'stream' => false,
        ]);

        return $response->json();
    }

    /**
     * @throws ConnectionException
     */
    public function unloadAll(): void
    {
        $this->unloadModel(config('app.ollama.model'));
        $this->unloadModel(config('app.ollama.embedding'));
    }

    /**
     * @throws ConnectionException
     */
    public function unloadModel(string $model): array
    {
        $response = $this->request()->post('/generate', [
            'model' => $model,
            'keep_alive' => 0,
            'stream' => false,
        ]);

        return $response->json();
    }

    private function request(): PendingRequest
    {
        return Http::timeout(60 * 1)
            ->baseUrl(sprintf('%s/api', config('app.ollama.url')))
            ->asJson()
            ->throw();
    }
}
