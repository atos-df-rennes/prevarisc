<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/application',
    ])
    ->withPhpVersion(PhpVersion::PHP_71)
    ->withPhpSets(php71: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        earlyReturn: true,
    )
    ->withSkip([
        RemoveUnusedPromotedPropertyRector::class,
        ChangeAndIfToEarlyReturnRector::class,
        CompleteDynamicPropertiesRector::class,
        RemoveAlwaysTrueIfConditionRector::class => [
            __DIR__.'/application/services/Descriptif.php',
        ],
    ])
;
