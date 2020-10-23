<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Strategy;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

/**
 * @internal
 */
interface ResolutionStrategy
{
    /**
     * @return mixed
     */
    public function resolveFieldValue(
        Generator $faker,
        FixtureFactory $fixtureFactory,
        FieldDefinition\Resolvable $fieldDefinition
    );

    public function resolveCount(Generator $faker, Count $count): int;
}
