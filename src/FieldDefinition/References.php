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
use Ergebnis\FactoryBot\Number;

/**
 * @internal
 *
 * @phpstan-template T
 *
 * @psalm-template T
 */
final class References implements Resolvable
{
    /**
     * @phpstan-var class-string<T>
     *
     * @psalm-var class-string<T>
     *
     * @var string
     */
    private $className;

    /**
     * @var Number\Exact
     */
    private $number;

    /**
     * @phpstan-param class-string<T> $className
     *
     * @psalm-param class-string<T> $className
     *
     * @param string       $className
     * @param Number\Exact $number
     */
    public function __construct(string $className, Number\Exact $number)
    {
        $this->className = $className;
        $this->number = $number;
    }

    /**
     * @phpstan-return array<int, T>
     *
     * @psalm-return list<T>
     *
     * @param FixtureFactory $fixtureFactory
     *
     * @return array<int, object>
     */
    public function resolve(FixtureFactory $fixtureFactory): array
    {
        return $fixtureFactory->createMany(
            $this->className,
            $this->number
        );
    }
}
