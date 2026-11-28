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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot;
use Faker\Generator;

/**
 * @internal
 */
final class Closure implements Resolvable
{
    public function __construct(private readonly \Closure $closure)
    {
    }

    public function resolve(
        Generator $faker,
        FactoryBot\FixtureFactory $fixtureFactory,
    ) {
        $closure = $this->closure;

        return $closure(
            $faker,
            $fixtureFactory,
        );
    }
}
