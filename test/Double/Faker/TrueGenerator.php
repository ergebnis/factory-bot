<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Double\Faker;

use Faker\Generator;

final class TrueGenerator extends Generator
{
    /**
     * @param int $chanceOfGettingTrue
     */
    public function boolean($chanceOfGettingTrue = 50): bool
    {
        return true;
    }

    /**
     * @param int $min
     * @param int $max
     */
    public function numberBetween($min = 0, $max = 2147483647): int
    {
        return \mt_rand(
            $min,
            $max,
        );
    }
}
