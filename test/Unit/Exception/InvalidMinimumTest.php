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

namespace Ergebnis\FactoryBot\Test\Unit\Exception;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Exception\InvalidMinimum
 */
final class InvalidMinimumTest extends Framework\TestCase
{
    use Helper;

    public function testNotGreaterThanOrEqualToReturnsException(): void
    {
        $minimum = -1 * self::faker()->numberBetween(1);

        $exception = Exception\InvalidMinimum::notGreaterThanOrEqualToZero($minimum);

        $message = \sprintf(
            'Minimum needs to be greater than or equal to 0, but %d is not.',
            $minimum
        );

        self::assertInstanceOf(Exception\InvalidMinimum::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
