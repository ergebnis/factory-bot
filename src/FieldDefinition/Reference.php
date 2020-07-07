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
final class Reference implements Resolvable
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
     * @var bool
     */
    private $isRequired;

    /**
     * @phpstan-param class-string<T> $className
     *
     * @psalm-param class-string<T> $className
     *
     * @param string $className
     * @param bool   $isRequired
     */
    private function __construct(string $className, bool $isRequired)
    {
        $this->className = $className;
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
     *
     * @return self
     */
    public static function required(string $className): self
    {
        return new self(
            $className,
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
     *
     * @return self
     */
    public static function optional(string $className): self
    {
        return new self(
            $className,
            false
        );
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * @phpstan-return T
     *
     * @psalm-return T
     *
     * @param FixtureFactory $fixtureFactory
     *
     * @return object
     */
    public function resolve(FixtureFactory $fixtureFactory)
    {
        return $fixtureFactory->create($this->className);
    }
}
