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

namespace Ergebnis\FactoryBot\Test\Unit\Exception;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Exception\InvalidMaximum::class)]
final class InvalidMaximumTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNotGreaterThanMinimumReturnsException(): void
    {
        $minimum = self::faker()->numberBetween(1);
        $maximum = $minimum - 1;

        $exception = Exception\InvalidMaximum::notGreaterThanMinimum(
            $minimum,
            $maximum,
        );

        $message = \sprintf(
            'Maximum needs to be greater than minimum %d, but %d is not.',
            $minimum,
            $maximum,
        );

        self::assertInstanceOf(Exception\InvalidMaximum::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
