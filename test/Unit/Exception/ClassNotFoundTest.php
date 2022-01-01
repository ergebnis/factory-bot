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
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Exception\ClassNotFound
 */
final class ClassNotFoundTest extends Framework\TestCase
{
    public function testNameReturnsException(): void
    {
        $className = 'foo';

        $exception = Exception\ClassNotFound::name($className);

        $message = \sprintf(
            'A class with the name "%s" could not be found.',
            $className,
        );

        self::assertInstanceOf(Exception\ClassNotFound::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
