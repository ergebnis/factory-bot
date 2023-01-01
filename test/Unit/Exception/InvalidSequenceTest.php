<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2023 Andreas Möller
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

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Exception\InvalidSequence
 */
final class InvalidSequenceTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testValueReturnsException(): void
    {
        $value = self::faker()->sentence();

        $exception = Exception\InvalidSequence::value($value);

        $message = \sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        );

        self::assertInstanceOf(Exception\InvalidSequence::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
