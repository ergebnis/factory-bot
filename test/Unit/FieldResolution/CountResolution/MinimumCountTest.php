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

#[Framework\Attributes\CoversClass(FieldResolution\CountResolution\MinimumCount::class)]
#[Framework\Attributes\UsesClass(Count::class)]
#[Framework\Attributes\UsesClass(FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Optional::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FixtureFactory::class)]
final class MinimumCountTest extends Test\Unit\AbstractTestCase
{
    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolveCountResolvesCountToValueWhenCountIsExact(int $value): void
    {
        $faker = self::faker();

        $strategy = new FieldResolution\CountResolution\MinimumCount();

        $resolved = $strategy->resolveCount(
            $faker,
            Count::exact($value),
        );

        self::assertSame($value, $resolved);
    }

    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolveCountResolvesCountToMinimumWhenCountIsBetween(int $minimum): void
    {
        $faker = self::faker();

        $maximum = $faker->numberBetween($minimum + 1);

        $strategy = new FieldResolution\CountResolution\MinimumCount();

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
