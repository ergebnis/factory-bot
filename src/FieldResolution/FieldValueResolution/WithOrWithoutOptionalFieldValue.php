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

namespace Ergebnis\FactoryBot\FieldResolution\FieldValueResolution;

use Ergebnis\FactoryBot;
use Faker\Generator;

/**
 * @internal
 */
final class WithOrWithoutOptionalFieldValue implements FieldValueResolutionStrategy
{
    public function resolveFieldValue(
        Generator $faker,
        FactoryBot\FixtureFactory $fixtureFactory,
        FactoryBot\FieldDefinition\Resolvable $fieldDefinition,
    ) {
        if (
            $fieldDefinition instanceof FactoryBot\FieldDefinition\Optional
            && !$faker->boolean()
        ) {
            return null;
        }

        return $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );
    }
}
