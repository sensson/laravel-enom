<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Enom\AddEmptyConstructorCommentRector;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/src'])
    ->withRules([
        AddEmptyConstructorCommentRector::class,
    ]);
