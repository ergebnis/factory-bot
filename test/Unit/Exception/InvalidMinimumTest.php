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

use Ergebnis\FactoryBot;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\Exception\InvalidMinimum::class)]
final class InvalidMinimumTest extends Framework\TestCase
{
    use FactoryBot\Test\Util\Helper;

    public function testNotGreaterThanOrEqualToReturnsException(): void
    {
        $minimum = -1 * self::faker()->numberBetween(1);

        $exception = FactoryBot\Exception\InvalidMinimum::notGreaterThanOrEqualToZero($minimum);

        $message = \sprintf(
            'Minimum needs to be greater than or equal to 0, but %d is not.',
            $minimum,
        );

        self::assertInstanceOf(FactoryBot\Exception\InvalidMinimum::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
