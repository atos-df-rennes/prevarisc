<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector;
use Rector\Php71\Rector\FuncCall\CountOnNullRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/application',
    ]);

    // Defined PHP version
    $rectorConfig->phpVersion(PhpVersion::PHP_71);

    // Rules Sets
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_71,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::CODE_QUALITY,
    ]);

    // Rules Skipped
    $rectorConfig->skip([
        CountOnNullRector::class,
        RemoveUnusedPromotedPropertyRector::class,
        ChangeAndIfToEarlyReturnRector::class,
        CompleteDynamicPropertiesRector::class,
    ]);
};
