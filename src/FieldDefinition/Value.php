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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

/**
 * @internal
 *
 * @phpstan-template T
 *
 * @psalm-template T
 */
final class Value implements Resolvable
{
    /**
     * @phpstan-var T
     *
     * @psalm-var T
     *
     * @var mixed
     */
    private $value;

    /**
     * @phpstan-param T $value
     *
     * @psalm-param T $value
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @phpstan-return T
     *
     * @psalm-return T
     *
     * @return mixed
     */
    public function resolve(Generator $faker, FixtureFactory $fixtureFactory)
    {
        return $this->value;
    }
}
