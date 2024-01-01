<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2024 Andreas Möller
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

#[Framework\Attributes\CoversClass(Exception\InvalidDefinition::class)]
final class InvalidDefinitionTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCanNotBeAutoloadedReturnsException(): void
    {
        $className = self::faker()->word();

        $exception = Exception\InvalidDefinition::canNotBeAutoloaded($className);

        self::assertInstanceOf(Exception\InvalidDefinition::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);

        $message = \sprintf(
            'Definition "%s" can not be autoloaded.',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testCanNotBeInstantiatedReturnsException(): void
    {
        $className = self::faker()->word();

        $exception = Exception\InvalidDefinition::canNotBeInstantiated($className);

        self::assertInstanceOf(Exception\InvalidDefinition::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);

        $message = \sprintf(
            'Definition "%s" can not be instantiated.',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testThrowsExceptionDuringInstantiationReturnsException(): void
    {
        $className = self::faker()->word();
        $previousException = new \Exception();

        $exception = Exception\InvalidDefinition::throwsExceptionDuringInstantiation(
            $className,
            $previousException,
        );

        self::assertInstanceOf(Exception\InvalidDefinition::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);

        $message = \sprintf(
            'An exception was thrown while trying to instantiate definition "%s".',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($previousException, $exception->getPrevious());
    }
}
