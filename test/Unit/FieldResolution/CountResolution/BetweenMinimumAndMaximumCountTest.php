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

namespace Ergebnis\FactoryBot\Test\Unit\FieldResolution\CountResolution;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FieldResolution;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FieldResolution\CountResolution\BetweenMinimumAndMaximumCount::class)]
#[Framework\Attributes\UsesClass(Count::class)]
#[Framework\Attributes\UsesClass(FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Optional::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FixtureFactory::class)]
final class BetweenMinimumAndMaximumCountTest extends Test\Unit\AbstractTestCase
{
    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsExact(int $value): void
    {
        $strategy = new FieldResolution\CountResolution\BetweenMinimumAndMaximumCount();

        $resolved = $strategy->resolveCount(
            self::faker(),
            Count::exact($value),
        );

        self::assertSame($value, $resolved);
    }

    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsBetweenAndFakerReturnsMinimum(int $minimum): void
    {
        $maximum = self::faker()->numberBetween($minimum + 1);

        $strategy = new FieldResolution\CountResolution\BetweenMinimumAndMaximumCount();

        $resolved = $strategy->resolveCount(
            new Test\Double\Faker\MinimumGenerator(),
            Count::between(
                $minimum,
                $maximum,
            ),
        );

        self::assertSame($minimum, $resolved);
    }

    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\IntProvider::class, 'greaterThanZero')]
    public function testResolveCountResolvesCountWithFakerWhenCountIsBetweenAndFakerReturnsMaximum(int $maximum): void
    {
        $minimum = self::faker()->numberBetween(0, $maximum - 1);

        $strategy = new FieldResolution\CountResolution\BetweenMinimumAndMaximumCount();

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

        $strategy = new FieldResolution\CountResolution\BetweenMinimumAndMaximumCount();

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
