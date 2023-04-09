<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php71\Rector\FuncCall\CountOnNullRector;
use Rector\Php74\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Php81\Rector\ClassMethod\NewInInitializerRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $containerConfigurator): void {
    $containerConfigurator->autoloadPaths([
        __DIR__ . '/src'
    ]);
    $containerConfigurator->skip([
        ClassPropertyAssignToConstructorPromotionRector::class,
        NullToStrictStringFuncCallArgRector::class,
        ChangeSwitchToMatchRector::class,
        CountOnNullRector::class,
        FirstClassCallableRector::class,
        NewInInitializerRector::class,
        ArraySpreadInsteadOfArrayMergeRector::class,
    ]);

    $containerConfigurator->import(LevelSetList::UP_TO_PHP_82);
};
