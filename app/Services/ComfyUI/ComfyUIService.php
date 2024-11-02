<?php

namespace App\Services\ComfyUI;

use App\Data\Assets;
use App\Data\FileDescriptor;
use App\Data\Tokens;
use App\Models\Book;
use App\Services\Traits\Resolvable;
use Exception;
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
    public function execute(string $workflow, Book $book)
    {
        $tokens = Tokens::make()
            ->add_token(':title:', $book->title)
            ->add_token(':subject:', $book->subject);

        foreach ($book->paragraphs as $index => $paragraph) {
            $tokens->add_token(sprintf(':paragraph-%s:', ++$index), $paragraph);
        }

        $workflowId = $this->prompt(
            prompt: $this->prepareWorkflow($workflow, $tokens),
            clientId: '6d46451b65c843e19f07e4d4c2bf8dac', //$book->id
        );

        $book->assets = $this->fetchOutputs($workflowId);
        $book->save();

    }

    /**
     * @throws ConnectionException
     */
    public function prompt(Collection $prompt, string $clientId): string
    {
        $response = $this->request()->post('/prompt', [
            'prompt' => $prompt->toArray(),
            'client_id' => $clientId
        ]);

        return $response->json('prompt_id');
    }

    /**
     * @return Collection<int, FileDescriptor>
     *
     * @throws ConnectionException
     * @throws Throwable
     */
    public function fetchOutputs(string $id): Collection
    {
        return retry(
            times: 100,
            callback: function () use ($id) {

                $response = $this->request()->get("/history/$id");

                $isCompleted = $response->json("$id.status.completed");

                if ($isCompleted) {

                    return $response
                        ->collect("$id.outputs")
                        ->flatten(2)
                        ->map(fn(array $output) => FileDescriptor::from($output))
                        ->mapWithKeys(fn(FileDescriptor $file) => [
                            $file->name() => $this->downloadImage($file)
                        ]);

                }

                throw new Exception('try again...');

            },
            sleepMilliseconds: 1000 * 5
        );
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
        $original = $this->getWorkflow($workflow);

        $filtered = $original
            ->filter(fn(array $item) => $item[ 'class_type' ] === 'CLIPTextEncode')
            ->map(function (array $item) use ($tokens) {

                data_set(
                    target: $item,
                    key: 'inputs.text',
                    value: $tokens->apply(data_get($item, 'inputs.text'))
                );

                return $item;

            });

        return $original->replace($filtered);
    }

    private function getWorkflow(string $name): Collection
    {
        return collect(json_decode(
            json: file_get_contents(base_path("app/Services/ComfyUI/Workflows/$name")),
            associative: true
        ));
    }

    private function request(): PendingRequest
    {
        return Http::timeout(60 * 5)
            ->baseUrl('http://comfy:8188')
            ->asJson()
            ->throw();
    }
}
