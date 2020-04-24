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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\FixtureFactory;

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
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @phpstan-param T $value
     * @phpstan-return self<T>
     *
     * @psalm-param T $value
     * @psalm-return self<T>
     *
     * @param mixed $value
     *
     * @return self
     */
    public static function required($value): self
    {
        return new self($value);
    }

    /**
     * @phpstan-return T
     *
     * @psalm-return T
     *
     * @param FixtureFactory $fixtureFactory
     *
     * @return mixed
     */
    public function resolve(FixtureFactory $fixtureFactory)
    {
        return $this->value;
    }
}
