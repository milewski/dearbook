<?php

declare(strict_types = 1);

use DigitalCreative\ECS\ValueObject\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {

    $config->parallel();
    $config->paths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ]);

    $config->skip([
        __DIR__ . '/bootstrap/cache',
    ]);

    $config->import(SetList::PHP_CS_FIXER);
    $config->import(SetList::CUSTOM);

};
