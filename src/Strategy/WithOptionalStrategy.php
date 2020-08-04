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
final class WithOptionalStrategy implements ResolutionStrategy
{
    public function resolveFieldValue(
        Generator $faker,
        FixtureFactory $fixtureFactory,
        FieldDefinition\Resolvable $fieldDefinition
    ) {
        return $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );
    }

    public function resolveCount(Generator $faker, Count $count): int
    {
        if ($count->minimum() === $count->maximum()) {
            return $count->minimum();
        }

        $resolved = $faker->numberBetween(
            $count->minimum(),
            $count->maximum()
        );

        if (0 === $resolved) {
            return 1;
        }

        return $resolved;
    }
}
