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

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

/**
 * @internal
 *
 * @template T of object
 */
final class References implements Resolvable
{
    /**
     * @param class-string<T>                          $className
     * @param array<string, \Closure|mixed|Resolvable> $fieldDefinitionOverrides
     */
    public function __construct(
        private string $className,
        private Count $count,
        private array $fieldDefinitionOverrides = [],
    ) {
    }

    /**
     * @return list<T>
     */
    public function resolve(
        Generator $faker,
        FixtureFactory $fixtureFactory,
    ): array {
        return $fixtureFactory->createMany(
            $this->className,
            $this->count,
            $this->fieldDefinitionOverrides,
        );
    }
}
