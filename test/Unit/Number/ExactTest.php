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

namespace Ergebnis\FactoryBot\Test\Unit\Number;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\Number\Exact;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Number\Exact
 *
 * @uses \Ergebnis\FactoryBot\Exception\InvalidNumber
 */
final class ExactTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::lessThanZero()
     *
     * @param int $value
     */
    public function testConstructorRejectsInvalidValue(int $value): void
    {
        $this->expectException(Exception\InvalidNumber::class);
        $this->expectExceptionMessage(\sprintf(
            'Number needs to be greater than or equal to %d, but %d is not.',
            0,
            $value
        ));

        new Exact($value);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testConstructorSetsValue(int $value): void
    {
        $number = new Exact($value);

        self::assertSame($value, $number->value());
    }
}
