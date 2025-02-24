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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

/**
 * @internal
 *
 * @template T of mixed
 */
final class Value implements Resolvable
{
    /**
     * @param T $value
     */
    public function __construct(private mixed $value)
    {
    }

    /**
     * @return T
     */
    public function resolve(
        Generator $faker,
        FixtureFactory $fixtureFactory,
    ) {
        return $this->value;
    }
}
