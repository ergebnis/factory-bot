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

#[Framework\Attributes\CoversClass(FactoryBot\Exception\InvalidDefinition::class)]
final class InvalidDefinitionTest extends Framework\TestCase
{
    use FactoryBot\Test\Util\Helper;

    public function testCanNotBeAutoloadedReturnsException(): void
    {
        $className = self::faker()->word();

        $exception = FactoryBot\Exception\InvalidDefinition::canNotBeAutoloaded($className);

        self::assertInstanceOf(FactoryBot\Exception\InvalidDefinition::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);

        $message = \sprintf(
            'Definition "%s" can not be autoloaded.',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
    }

    public function testCanNotBeInstantiatedReturnsException(): void
    {
        $className = self::faker()->word();

        $exception = FactoryBot\Exception\InvalidDefinition::canNotBeInstantiated($className);

        self::assertInstanceOf(FactoryBot\Exception\InvalidDefinition::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);

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

        $exception = FactoryBot\Exception\InvalidDefinition::throwsExceptionDuringInstantiation(
            $className,
            $previousException,
        );

        self::assertInstanceOf(FactoryBot\Exception\InvalidDefinition::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);

        $message = \sprintf(
            'An exception was thrown while trying to instantiate definition "%s".',
            $className,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($previousException, $exception->getPrevious());
    }
}
