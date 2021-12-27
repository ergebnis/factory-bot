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

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

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
     */
    private string $className;
    private Count $count;

    /**
     * @phpstan-var array<string, \Closure|mixed|Resolvable>
     *
     * @psalm-var array<string, \Closure|mixed|Resolvable>
     *
     * @var array<string, \Closure|mixed|Resolvable>
     */
    private array $fieldDefinitionOverrides;

    /**
     * @phpstan-param class-string<T> $className
     * @phpstan-param array<string, \Closure|mixed|Resolvable> $fieldDefinitionOverrides
     *
     * @psalm-param class-string<T> $className
     * @psalm-param array<string, \Closure|mixed|Resolvable> $fieldDefinitionOverrides
     *
     * @param array<string, \Closure|mixed|Resolvable> $fieldDefinitionOverrides
     */
    public function __construct(
        string $className,
        Count $count,
        array $fieldDefinitionOverrides = []
    ) {
        $this->className = $className;
        $this->count = $count;
        $this->fieldDefinitionOverrides = $fieldDefinitionOverrides;
    }

    /**
     * @phpstan-return array<int, T>
     *
     * @psalm-return list<T>
     *
     * @return array<int, object>
     */
    public function resolve(
        Generator $faker,
        FixtureFactory $fixtureFactory
    ): array {
        return $fixtureFactory->createMany(
            $this->className,
            $this->count,
            $this->fieldDefinitionOverrides,
        );
    }
}
