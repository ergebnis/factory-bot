<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2023 Andreas Möller
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
use Ergebnis\FactoryBot\Strategy;
use Ergebnis\FactoryBot\Test;

#[\PHPUnit\Framework\Attributes\CoversClass(\Ergebnis\FactoryBot\Strategy\DefaultStrategy::class)]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\Count')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\Optional')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\Value')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FixtureFactory')]
final class DefaultStrategyTest extends Test\Unit\AbstractTestCase
{
    public function testResolveFieldValueResolvesOptionalFieldDefinitionWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FieldDefinition::optionalValue($faker->sentence());

        $strategy = new Strategy\DefaultStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Test\Double\Faker\TrueGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveFieldValueResolvesOptionalFieldDefinitionToNullWhenFakerReturnsFalse(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FieldDefinition::optionalValue($faker->sentence());

        $strategy = new Strategy\DefaultStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Test\Double\Faker\FalseGenerator(),
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

        $fieldDefinition = FieldDefinition::value($faker->sentence());

        $strategy = new Strategy\DefaultStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Test\Double\Faker\TrueGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveFieldValueResolvesFieldDefinitionWithFakerAndFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FieldDefinition::value($faker->sentence());

        $strategy = new Strategy\DefaultStrategy();

        $resolved = $strategy->resolveFieldValue(
            new Test\Double\Faker\FalseGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertSame($expected, $resolved);
    }

    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsExact(int $value): void
    {
        $strategy = new Strategy\DefaultStrategy();

        $resolved = $strategy->resolveCount(
            self::faker(),
            Count::exact($value),
        );

        self::assertSame($value, $resolved);
    }

    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsBetweenAndFakerReturnsMinimum(int $minimum): void
    {
        $maximum = self::faker()->numberBetween($minimum + 1);

        $strategy = new Strategy\DefaultStrategy();

        $resolved = $strategy->resolveCount(
            new Test\Double\Faker\MinimumGenerator(),
            Count::between(
                $minimum,
                $maximum,
            ),
        );

        self::assertSame($minimum, $resolved);
    }

    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsBetweenAndFakerReturnsMaximum(int $maximum): void
    {
        $minimum = self::faker()->numberBetween(0, $maximum - 1);

        $strategy = new Strategy\DefaultStrategy();

        $resolved = $strategy->resolveCount(
            new Test\Double\Faker\MaximumGenerator(),
            Count::between(
                $minimum,
                $maximum,
            ),
        );

        self::assertSame($maximum, $resolved);
    }

    public function testResolveCountResolvesCountWithFakerWhenCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1);
        $maximum = $faker->numberBetween($minimum + 1);

        $strategy = new Strategy\DefaultStrategy();

        $resolved = $strategy->resolveCount(
            $faker,
            Count::between(
                $minimum,
                $maximum,
            ),
        );

        self::assertGreaterThanOrEqual($minimum, $resolved);
        self::assertLessThanOrEqual($maximum, $resolved);
    }
}
