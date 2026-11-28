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

namespace Ergebnis\FactoryBot\Test\Unit;

use Ergebnis\FactoryBot;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\Count::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Exception\InvalidCount::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Exception\InvalidMaximum::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Exception\InvalidMinimum::class)]
final class CountTest extends Framework\TestCase
{
    use FactoryBot\Test\Util\Helper;

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'lessThanZero')]
    public function testExactRejectsValueLessThanZero(int $value): void
    {
        $this->expectException(FactoryBot\Exception\InvalidCount::class);
        $this->expectExceptionMessage(\sprintf(
            'Count needs to be greater than or equal to 0, but %d is not.',
            $value,
        ));

        FactoryBot\Count::exact($value);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testExactReturnsCountWhenValueIsGreaterThanZero(int $value): void
    {
        $count = FactoryBot\Count::exact($value);

        self::assertInstanceOf(FactoryBot\Count::class, $count);
        self::assertSame($value, $count->minimum());
        self::assertSame($value, $count->maximum());
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'lessThanZero')]
    public function testBetweenRejectsMinimumLessThanZero(int $minimum): void
    {
        $maximum = $minimum + 1;

        $this->expectException(FactoryBot\Exception\InvalidMinimum::class);
        $this->expectExceptionMessage(\sprintf(
            'Minimum needs to be greater than or equal to 0, but %d is not.',
            $minimum,
        ));

        FactoryBot\Count::between(
            $minimum,
            $maximum,
        );
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testBetweenRejectsMaximumNotGreaterThanMinimum(int $difference): void
    {
        $minimum = self::faker()->numberBetween(1);
        $maximum = $minimum - $difference;

        $this->expectException(FactoryBot\Exception\InvalidMaximum::class);
        $this->expectExceptionMessage(\sprintf(
            'Maximum needs to be greater than minimum %d, but %d is not.',
            $minimum,
            $maximum,
        ));

        FactoryBot\Count::between(
            $minimum,
            $maximum,
        );
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testBetweenReturnsCountWhenMinimumIsGreaterThanOrEqualToZeroAndMaximumIsGreaterThanMinimum(int $minimum): void
    {
        $faker = self::faker();

        $maximum = $minimum + $faker->numberBetween(1);

        $count = FactoryBot\Count::between(
            $minimum,
            $maximum,
        );

        self::assertInstanceOf(FactoryBot\Count::class, $count);
        self::assertSame($minimum, $count->minimum());
        self::assertSame($maximum, $count->maximum());
    }
}
