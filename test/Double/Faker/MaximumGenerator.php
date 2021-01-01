<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Double\Faker;

use Faker\Generator;

final class MaximumGenerator extends Generator
{
    public function numberBetween($min = 0, $max = 2147483647): int
    {
        return $max;
    }
}
