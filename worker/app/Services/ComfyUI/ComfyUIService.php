<?php

declare(strict_types = 1);

namespace App\Services\ComfyUI;

use App\Data\AssetsWork;
use App\Data\FileDescriptor;
use App\Data\Tokens;
use App\Services\Traits\Resolvable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\FilesystemException;
use Throwable;

class ComfyUIService
{
    use Resolvable;

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    public function execute(string $workflow, AssetsWork $work): string
    {
        $tokens = Tokens::make()
            ->add_token(':title:', $work->title)
            ->add_token(':synopsis:', $work->synopsis);

        foreach ($work->illustrations as $index => $illustration) {
            $tokens->add_token(sprintf(':illustration-%s:', ++$index), $illustration);
        }

        return $this->prompt(
            prompt: $this->prepareWorkflow($workflow, $tokens),
            clientId: Str::uuid()->toString(),
        );
    }

    /**
     * @throws ConnectionException
     */
    public function prompt(Collection $prompt, string $clientId): string
    {
        $response = $this->request()->post('/prompt', [
            'prompt' => $prompt->toArray(),
            'client_id' => $clientId,
        ]);

        return $response->json('prompt_id');
    }

    /**
     * @throws ConnectionException
     */
    public function deleteWorkflow(string $workflowId): bool
    {
        $response = $this->request()->post('/history', [
            'delete' => [ $workflowId ],
        ]);

        return $response->successful();
    }

    /**
     * @throws FilesystemException
     * @throws ConnectionException
     */
    public function fetchOutputs(string $id): Collection|bool
    {
        $response = $this->request()->get("/history/$id");
        $isCompleted = $response->json("$id.status.completed");

        /**
         * Workflow has not completed yet
         */
        if ($isCompleted === null) {
            return true;
        }

        /**
         * Workflow failed...so invalidate the workflow and regenerate it again...
         */
        if ($isCompleted === false) {
            return false;
        }

        $assets = $response
            ->collect("$id.outputs")
            ->flatten(2)
            ->map(fn (array $output) => FileDescriptor::from($output))
            ->mapWithKeys(fn (FileDescriptor $file) => [
                $file->name() => $this->downloadImage($file),
            ]);

        $this->deleteWorkflow($id);

        return $assets;
    }

    /**
     * @throws ConnectionException
     * @throws FilesystemException
     */
    public function downloadImage(FileDescriptor $fileDescription): string
    {
        $response = $this->request()->get('/view', $fileDescription->toArray());
        $body = $response->body();

        Storage::disk('public')->write($path = sprintf('%s.png', md5($body)), $body);

        return $path;
    }

    private function prepareWorkflow(string $workflow, Tokens $tokens): Collection
    {
        return $this->getWorkflow($workflow)->map(function (array $item) use ($tokens) {

            if ($item[ 'class_type' ] === 'KSampler') {
                data_set($item, 'inputs.seed', random_int(0, PHP_INT_MAX));
            }

            if ($item[ 'class_type' ] === 'CLIPTextEncode') {
                data_set($item, 'inputs.text', $tokens->apply(data_get($item, 'inputs.text')));
            }

            return $item;

        });
    }

    private function getWorkflow(string $name): Collection
    {
        return collect(json_decode(
            json: file_get_contents(base_path("app/Services/ComfyUI/Workflows/$name")),
            associative: true,
        ));
    }

    private function request(): PendingRequest
    {
        return Http::timeout(60 * 5)
            ->baseUrl(config('app.comfyui_internal_url'))
            ->asJson()
            ->throw();
    }
}
