<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return function (RectorConfig $rectorConfig) {
    $rectorConfig->sets([
        SetList::DEAD_CODE,
        LevelSetList::UP_TO_PHP_81,
    ]);

    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/database',
    ]);
};
