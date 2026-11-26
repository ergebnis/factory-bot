<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

use Ergebnis\Rector;
use Rector\Doctrine;

$ruleSet = Rector\Config\RuleSet\Php81::create();

return Rector\Config\Factory::fromRuleSet($ruleSet)
    ->withCache(__DIR__ . '/.build/rector/')
    ->withPaths([
        __DIR__ . '/config/',
        __DIR__ . '/example/',
        __DIR__ . '/src/',
        __DIR__ . '/test/',
        __DIR__ . '/.php-cs-fixer.php',
        __DIR__ . '/rector.php',
    ])
    ->withSets([
        Doctrine\Set\DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ])
    ->withSkip([
        __DIR__ . '/example/src/Entity/',
    ]);
