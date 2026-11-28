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

#[Framework\Attributes\CoversClass(FactoryBot\Exception\ClassNotFound::class)]
final class ClassNotFoundTest extends Framework\TestCase
{
    public function testNameReturnsException(): void
    {
        $className = 'foo';

        $exception = FactoryBot\Exception\ClassNotFound::name($className);

        $message = \sprintf(
            'A class with the name "%s" could not be found.',
            $className,
        );

        self::assertInstanceOf(FactoryBot\Exception\ClassNotFound::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
