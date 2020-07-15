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

namespace Ergebnis\FactoryBot\Test\Unit\FieldValue;

use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FieldValue\DefaultResolutionStrategy;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Double;
use Ergebnis\FactoryBot\Test\Unit;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldValue\DefaultResolutionStrategy
 *
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Optional
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class DefaultResolutionStrategyTest extends Unit\AbstractTestCase
{
    public function testResolveReturnsNullWhenFieldDefinitionIsOptionalAndFakerReturnsFalse(): void
    {
        $fieldDefinition = FieldDefinition::optionalValue(self::faker()->url);

        $faker = new Double\Faker\FalseGenerator();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $strategy = new DefaultResolutionStrategy();

        $resolved = $strategy->resolve(
            $fieldDefinition,
            $faker,
            $fixtureFactory
        );

        self::assertNull($resolved);
    }

    public function testResolveReturnsValueWhenFieldDefinitionIsOptionalAndFakerReturnsTrue(): void
    {
        $fieldDefinition = FieldDefinition::optionalValue(self::faker()->url);

        $faker = new Double\Faker\TrueGenerator();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $strategy = new DefaultResolutionStrategy();

        $resolved = $strategy->resolve(
            $fieldDefinition,
            $faker,
            $fixtureFactory
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveResolvesFieldDefinitionWhenFieldDefinitionIsNotOptionalAndFakerReturnsFalse(): void
    {
        $fieldDefinition = FieldDefinition::value(self::faker()->url);

        $faker = new Double\Faker\FalseGenerator();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $strategy = new DefaultResolutionStrategy();

        $resolved = $strategy->resolve(
            $fieldDefinition,
            $faker,
            $fixtureFactory
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveResolvesFieldDefinitionWhenFieldDefinitionIsNotOptionalAndFakerReturnsTrue(): void
    {
        $fieldDefinition = FieldDefinition::optionalValue(self::faker()->url);

        $faker = new Double\Faker\TrueGenerator();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $strategy = new DefaultResolutionStrategy();

        $resolved = $strategy->resolve(
            $fieldDefinition,
            $faker,
            $fixtureFactory
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertSame($expected, $resolved);
    }
}
