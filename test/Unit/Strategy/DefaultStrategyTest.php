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

namespace Ergebnis\FactoryBot\Test\Unit\Strategy;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Strategy\DefaultStrategy;
use Ergebnis\FactoryBot\Test\Double;
use Ergebnis\FactoryBot\Test\Unit;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Strategy\DefaultStrategy
 *
 * @uses \Ergebnis\FactoryBot\Count
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Optional
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class DefaultStrategyTest extends Unit\AbstractTestCase
{
    public function testResolveFieldValueResolvesOptionalFieldDefinitionWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fieldDefinition = FieldDefinition::optionalValue($faker->sentence);

        $strategy = new DefaultStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Double\Faker\TrueGenerator(),
            $fixtureFactory,
            $fieldDefinition
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveFieldValueResolvesOptionalFieldDefinitionToNullWhenFakerReturnsFalse(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fieldDefinition = FieldDefinition::optionalValue($faker->sentence);

        $strategy = new DefaultStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Double\Faker\FalseGenerator(),
            $fixtureFactory,
            $fieldDefinition
        );

        self::assertNull($resolved);
    }

    public function testResolveFieldValueResolvesFieldDefinitionWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fieldDefinition = FieldDefinition::value($faker->sentence);

        $strategy = new DefaultStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Double\Faker\TrueGenerator(),
            $fixtureFactory,
            $fieldDefinition
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveFieldValueResolvesFieldDefinitionWithFakerAndFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fieldDefinition = FieldDefinition::value($faker->sentence);

        $strategy = new DefaultStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Double\Faker\FalseGenerator(),
            $fixtureFactory,
            $fieldDefinition
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveCountResolvesCountWithFakerWhenCountIsExact(): void
    {
        $faker = self::faker();

        $value = $faker->numberBetween(1);

        $strategy = new DefaultStrategy();

        $resolved = $strategy->resolveCount(
            $faker,
            Count::exact($value)
        );

        self::assertSame($value, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $minimum
     */
    public function testResolveCountResolvesCountWithFakerWhenCountIsBetweenAndFakerReturnsMinimum(int $minimum): void
    {
        $maximum = self::faker()->numberBetween($minimum + 1);

        $strategy = new DefaultStrategy();

        $resolved = $strategy->resolveCount(
            new Double\Faker\MinimumGenerator(),
            Count::between(
                $minimum,
                $maximum
            )
        );

        self::assertSame($minimum, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanZero()
     *
     * @param int $maximum
     */
    public function testResolveCountResolvesCountWithFakerWhenCountIsBetweenAndFakerReturnsMaximum(int $maximum): void
    {
        $minimum = self::faker()->numberBetween(0, $maximum - 1);

        $strategy = new DefaultStrategy();

        $resolved = $strategy->resolveCount(
            new Double\Faker\MaximumGenerator(),
            Count::between(
                $minimum,
                $maximum
            )
        );

        self::assertSame($maximum, $resolved);
    }

    public function testResolveCountResolvesCountWithFakerWhenCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1);
        $maximum = $faker->numberBetween($minimum + 1);

        $strategy = new DefaultStrategy();

        $resolved = $strategy->resolveCount(
            $faker,
            Count::between(
                $minimum,
                $maximum
            )
        );

        self::assertGreaterThanOrEqual($minimum, $resolved);
        self::assertLessThanOrEqual($maximum, $resolved);
    }
}
