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

namespace Ergebnis\FactoryBot\Test\Unit\FieldDefinition;

use Ergebnis\FactoryBot\FieldDefinition\Optional;
use Ergebnis\FactoryBot\FieldDefinition\Resolvable;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Unit;
use Example\Entity;
use Faker\Generator;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition\Optional
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 * @uses \Ergebnis\FactoryBot\Strategy\DefaultStrategy
 */
final class OptionalTest extends Unit\AbstractTestCase
{
    public function testResolvesToResultOfResolvingResolvableWithFixtureFactory(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Entity\User::class);

        $resolvable = new class() implements Resolvable {
            public function resolve(Generator $faker, FixtureFactory $fixtureFactory)
            {
                return $fixtureFactory->createOne(Entity\User::class);
            }
        };

        $fieldDefinition = new Optional($resolvable);

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertInstanceOf(Entity\User::class, $resolved);
    }
}
