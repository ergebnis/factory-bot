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

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FixtureFactory;

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
     * @var int
     */
    private $count;

    /**
     * @var bool
     */
    private $isRequired;

    /**
     * @phpstan-param class-string<T> $className
     *
     * @psalm-param class-string<T> $className
     *
     * @param string $className
     * @param int    $count
     * @param bool   $isRequired
     */
    private function __construct(string $className, int $count, bool $isRequired)
    {
        $this->className = $className;
        $this->count = $count;
        $this->isRequired = $isRequired;
    }

    /**
     * @phpstan-param class-string<T> $className
     * @phpstan-return self<T>
     *
     * @psalm-param class-string<T> $className
     * @psalm-return self<T>
     *
     * @param string $className
     * @param int    $count
     *
     * @throws Exception\InvalidCount
     *
     * @return self
     */
    public static function required(string $className, int $count): self
    {
        if (1 > $count) {
            throw Exception\InvalidCount::notGreaterThanOrEqualTo(
                1,
                $count
            );
        }

        return new self(
            $className,
            $count,
            true
        );
    }

    /**
     * @phpstan-param class-string<T> $className
     * @phpstan-return self<T>
     *
     * @psalm-param class-string<T> $className
     * @psalm-return self<T>
     *
     * @param string $className
     * @param int    $count
     *
     * @throws Exception\InvalidCount
     *
     * @return self
     */
    public static function optional(string $className, int $count): self
    {
        if (1 > $count) {
            throw Exception\InvalidCount::notGreaterThanOrEqualTo(
                1,
                $count
            );
        }

        return new self(
            $className,
            $count,
            false
        );
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
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
        return $fixtureFactory->createMultiple(
            $this->className,
            [],
            $this->count
        );
    }
}
