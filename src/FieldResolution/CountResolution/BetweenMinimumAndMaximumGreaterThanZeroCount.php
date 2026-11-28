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

namespace Ergebnis\FactoryBot\FieldResolution\CountResolution;

use Ergebnis\FactoryBot;
use Faker\Generator;

/**
 * @internal
 */
final class BetweenMinimumAndMaximumGreaterThanZeroCount implements CountResolutionStrategy
{
    public function resolveCount(
        Generator $faker,
        FactoryBot\Count $count,
    ): int {
        if ($count->minimum() === $count->maximum()) {
            return $count->minimum();
        }

        $resolved = $faker->numberBetween(
            $count->minimum(),
            $count->maximum(),
        );

        if (0 === $resolved) {
            return 1;
        }

        return $resolved;
    }
}
