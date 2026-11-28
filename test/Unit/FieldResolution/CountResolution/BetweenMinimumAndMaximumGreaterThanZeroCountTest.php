<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit\FieldResolution\CountResolution;

use Ergebnis\FactoryBot;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\FieldResolution\CountResolution\BetweenMinimumAndMaximumGreaterThanZeroCount::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Count::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Optional::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FixtureFactory::class)]
final class BetweenMinimumAndMaximumGreaterThanZeroCountTest extends FactoryBot\Test\Unit\AbstractTestCase
{
    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsExact(int $value): void
    {
        $faker = self::faker();

        $strategy = new FactoryBot\FieldResolution\CountResolution\BetweenMinimumAndMaximumGreaterThanZeroCount();

        $resolved = $strategy->resolveCount(
            $faker,
            FactoryBot\Count::exact($value),
        );

        self::assertSame($value, $resolved);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsBetweenAndFakerReturnsMinimum(int $minimum): void
    {
        $maximum = self::faker()->numberBetween($minimum + 1);

        $strategy = new FactoryBot\FieldResolution\CountResolution\BetweenMinimumAndMaximumGreaterThanZeroCount();

        $resolved = $strategy->resolveCount(
            new FactoryBot\Test\Double\Faker\MinimumGenerator(),
            FactoryBot\Count::between(
                $minimum,
                $maximum,
            ),
        );

        $minimumGreaterThanZero = \max(
            1,
            $minimum,
        );

        self::assertSame($minimumGreaterThanZero, $resolved);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsBetweenAndFakerReturnsMaximum(int $maximum): void
    {
        $minimum = self::faker()->numberBetween(0, $maximum - 1);

        $strategy = new FactoryBot\FieldResolution\CountResolution\BetweenMinimumAndMaximumGreaterThanZeroCount();

        $resolved = $strategy->resolveCount(
            new FactoryBot\Test\Double\Faker\MaximumGenerator(),
            FactoryBot\Count::between(
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

        $strategy = new FactoryBot\FieldResolution\CountResolution\BetweenMinimumAndMaximumGreaterThanZeroCount();

        $resolved = $strategy->resolveCount(
            $faker,
            FactoryBot\Count::between(
                $minimum,
                $maximum,
            ),
        );

        $minimumGreaterThanZero = \max(
            1,
            $minimum,
        );

        self::assertGreaterThanOrEqual($minimumGreaterThanZero, $resolved);
        self::assertLessThanOrEqual($maximum, $resolved);
    }
}
