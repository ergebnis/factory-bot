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

#[Framework\Attributes\CoversClass(FactoryBot\Exception\InvalidCount::class)]
final class InvalidCountTest extends Framework\TestCase
{
    use FactoryBot\Test\Util\Helper;

    public function testNotGreaterThanOrEqualToReturnsException(): void
    {
        $value = -1 * self::faker()->numberBetween(1);

        $exception = FactoryBot\Exception\InvalidCount::notGreaterThanOrEqualToZero($value);

        $message = \sprintf(
            'Count needs to be greater than or equal to 0, but %d is not.',
            $value,
        );

        self::assertInstanceOf(FactoryBot\Exception\InvalidCount::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
