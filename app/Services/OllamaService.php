<?php

namespace App\Services;

use App\Enums\Embedding;
use App\Enums\Model;
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
    public function generateJson(Model $model, string $prompt): Collection
    {
        $response = $this->request()->post('/generate', [
            'model' => $model->value,
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
    public function generateEmbedding(Embedding $embedding, array $inputs): Collection
    {
        return $this->request()->post('/embed', [
            'model' => $embedding->value,
            'input' => $inputs,
            'stream' => false,
        ])->collect('embeddings');
    }

    /**
     * https://github.com/ollama/ollama/blob/main/docs/api.md#pull-a-model
     *
     * @throws ConnectionException
     */
    public function pullModel(Model|Embedding $model): array
    {
        $response = $this->request()->post('/pull', [
            'name' => $model->value,
            'stream' => false,
        ]);

        return $response->json();
    }

    /**
     * @throws ConnectionException
     */
    public function unloadAll(): void
    {
        foreach (Model::cases() as $model) {
            $this->unloadModel($model);
        }

        foreach (Embedding::cases() as $model) {
            $this->unloadModel($model);
        }
    }

    /**
     * @throws ConnectionException
     */
    public function unloadModel(Model|Embedding $model): array
    {
        $response = $this->request()->post('/generate', [
            'model' => $model->value,
            'keep_alive' => 0,
            'stream' => false,
        ]);

        return $response->json();
    }

    private function request(): PendingRequest
    {
        return Http::timeout(60 * 5)
            ->baseUrl('http://ollama:11434/api/')
            ->asJson()
            ->throw();
    }
}
