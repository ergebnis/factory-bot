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

namespace Ergebnis\FactoryBot\Test\Unit;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\Number;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Number
 *
 * @uses \Ergebnis\FactoryBot\Exception\InvalidMaximum
 * @uses \Ergebnis\FactoryBot\Exception\InvalidMinimum
 * @uses \Ergebnis\FactoryBot\Exception\InvalidNumber
 */
final class NumberTest extends Framework\TestCase
{
    use Helper;

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::lessThanZero()
     *
     * @param int $value
     */
    public function testExactRejectsInvalidValue(int $value): void
    {
        $this->expectException(Exception\InvalidNumber::class);
        $this->expectExceptionMessage(\sprintf(
            'Number needs to be greater than or equal to 0, but %d is not.',
            $value
        ));

        Number::exact($value);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testExactReturnsNumberValue(int $value): void
    {
        $faker = self::faker();

        $number = Number::exact($value);

        self::assertInstanceOf(Number::class, $number);

        $resolved = $number->resolve($faker);

        self::assertSame($value, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::lessThanZero()
     *
     * @param int $minimum
     */
    public function testBetweenRejectsInvalidMinimum(int $minimum): void
    {
        $maximum = $minimum + 1;

        $this->expectException(Exception\InvalidMinimum::class);
        $this->expectExceptionMessage(\sprintf(
            'Minimum needs to be greater than or equal to 0, but %d is not.',
            $minimum
        ));

        Number::between(
            $minimum,
            $maximum
        );
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $difference
     */
    public function testBetweenRejectsInvalidMaximum(int $difference): void
    {
        $minimum = self::faker()->numberBetween(1);
        $maximum = $minimum - $difference;

        $this->expectException(Exception\InvalidMaximum::class);
        $this->expectExceptionMessage(\sprintf(
            'Maximum needs to be greater than minimum %d, but %d is not.',
            $minimum,
            $maximum
        ));

        Number::between(
            $minimum,
            $maximum
        );
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanZero()
     *
     * @param int $difference
     */
    public function testBetweenReturnsNumber(int $difference): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1);
        $maximum = $minimum + $difference;

        $number = Number::between(
            $minimum,
            $maximum
        );

        self::assertInstanceOf(Number::class, $number);

        $resolved = $number->resolve($faker);

        self::assertGreaterThanOrEqual($minimum, $resolved);
        self::assertLessThanOrEqual($maximum, $resolved);
    }
}
