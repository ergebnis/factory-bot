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
 * @phpstan-template T
 *
 * @psalm-template T
 */
final class Reference implements Resolvable
{
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
        private string $className,
        private array $fieldDefinitionOverrides = [],
    ) {
    }

    /**
     * @phpstan-return T
     *
     * @psalm-return T
     *
     * @return object
     */
    public function resolve(
        Generator $faker,
        FixtureFactory $fixtureFactory,
    ) {
        return $fixtureFactory->createOne(
            $this->className,
            $this->fieldDefinitionOverrides,
        );
    }
}
