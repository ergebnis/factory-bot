<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit\Exception;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\Test\Util;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Exception\InvalidCount
 */
final class InvalidCountTest extends Framework\TestCase
{
    use Util\Helper;

    public function testNotGreaterThanOrEqualToReturnsException(): void
    {
        $value = -1 * self::faker()->numberBetween(1);

        $exception = Exception\InvalidCount::notGreaterThanOrEqualToZero($value);

        $message = \sprintf(
            'Count needs to be greater than or equal to 0, but %d is not.',
            $value,
        );

        self::assertInstanceOf(Exception\InvalidCount::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
