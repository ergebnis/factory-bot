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

use Ergebnis\FactoryBot\Count;
use Faker\Generator;

/**
 * @internal
 */
interface CountResolutionStrategy
{
    public function resolveCount(
        Generator $faker,
        Count $count,
    ): int;
}
