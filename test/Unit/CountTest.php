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

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\Exception;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Count
 *
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 */
final class CountTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::lessThanOne()
     *
     * @param int $value
     */
    public function testConstructorRejectsInvalidValue(int $value): void
    {
        $this->expectException(Exception\InvalidCount::class);
        $this->expectExceptionMessage(\sprintf(
            'Count needs to be greater than or equal to %d, but %d is not.',
            1,
            $value
        ));

        new Count($value);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToOne()
     *
     * @param int $value
     */
    public function testConstructorSetsValue(int $value): void
    {
        $count = new Count($value);

        self::assertSame($value, $count->value());
    }
}
