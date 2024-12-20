<?php

declare(strict_types = 1);

namespace App\Data;

use App\Enums\GenerationType;
use Spatie\LaravelData\Data;

class AssetsWork extends Data
{
    public function __construct(
        public string $id,
        public string $title,
        public string $synopsis,
        public array $illustrations,
        public GenerationType $generationType,
        public GenerationDataSimple|GenerationDataAdvanced $generationData,
    )
    {
    }

    public static function fromResponse(array $data): self
    {
        return new static(
            id: $data[ 'id' ],
            title: $data[ 'title' ],
            synopsis: $data[ 'synopsis' ],
            illustrations: $data[ 'illustrations' ],
            generationType: $type = GenerationType::from($data[ 'generation_type' ]),
            generationData: match ($type) {
                GenerationType::Simple => GenerationDataSimple::from($data[ 'generation_data' ]),
                GenerationType::Advanced => GenerationDataAdvanced::from($data[ 'generation_data' ]),
            },
        );
    }

    public function workflow(): string
    {
        return match ($this->generationType) {
            GenerationType::Simple => 'main.simple.workflow.json',
            GenerationType::Advanced => 'main.advanced.workflow.json',
        };
    }

    public function tokens(): Tokens
    {
        $tokens = Tokens::make()
            ->add(':title:', $this->title)
            ->add(':synopsis:', $this->synopsis);

        foreach ($this->illustrations as $index => $illustration) {
            $tokens->add(sprintf(':illustration-%s:', ++$index), $illustration);
        }

        if ($this->generationType === GenerationType::Advanced) {
            $tokens->add(':negative:', $this->generationData->negative);
        }

        return $tokens;
    }
}
