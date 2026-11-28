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

#[Framework\Attributes\CoversClass(FactoryBot\Exception\InvalidSequence::class)]
final class InvalidSequenceTest extends Framework\TestCase
{
    use FactoryBot\Test\Util\Helper;

    public function testValueReturnsException(): void
    {
        $value = self::faker()->sentence();

        $exception = FactoryBot\Exception\InvalidSequence::value($value);

        $message = \sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        );

        self::assertInstanceOf(FactoryBot\Exception\InvalidSequence::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
