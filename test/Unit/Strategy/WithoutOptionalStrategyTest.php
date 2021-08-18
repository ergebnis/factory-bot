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

namespace Ergebnis\FactoryBot\Test\Unit\Strategy;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Strategy\WithoutOptionalStrategy;
use Ergebnis\FactoryBot\Test\Double;
use Ergebnis\FactoryBot\Test\Unit;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Strategy\WithoutOptionalStrategy
 *
 * @uses \Ergebnis\FactoryBot\Count
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Optional
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class WithoutOptionalStrategyTest extends Unit\AbstractTestCase
{
    public function testResolveFieldValueResolvesOptionalFieldDefinitionToNullWhenFakerReturnsTrue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FieldDefinition::optionalValue($faker->sentence);

        $strategy = new WithoutOptionalStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Double\Faker\TrueGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        self::assertNull($resolved);
    }

    public function testResolveFieldValueResolvesOptionalFieldDefinitionToNullWhenFakerReturnsFalse(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FieldDefinition::optionalValue($faker->sentence);

        $strategy = new WithoutOptionalStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Double\Faker\FalseGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        self::assertNull($resolved);
    }

    public function testResolveFieldValueResolvesFieldDefinitionWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FieldDefinition::value($faker->sentence);

        $strategy = new WithoutOptionalStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Double\Faker\TrueGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveFieldValueResolvesFieldDefinitionWithoutFakerAndFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FieldDefinition::value($faker->sentence);

        $strategy = new WithoutOptionalStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Double\Faker\FalseGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertSame($expected, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testResolveCountResolvesCountToValueWhenCountIsExact(int $value): void
    {
        $faker = self::faker();

        $strategy = new WithoutOptionalStrategy();

        $resolved = $strategy->resolveCount(
            $faker,
            Count::exact($value),
        );

        self::assertSame($value, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testResolveCountResolvesCountToMininumWhenCountIsBetween(int $minimum): void
    {
        $faker = self::faker();

        $maximum = $faker->numberBetween($minimum + 1);

        $strategy = new WithoutOptionalStrategy();

        $resolved = $strategy->resolveCount(
            $faker,
            Count::between(
                $minimum,
                $maximum,
            ),
        );

        self::assertSame($minimum, $resolved);
    }
}
