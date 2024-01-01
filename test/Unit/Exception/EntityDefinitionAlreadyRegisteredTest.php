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
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Exception\EntityDefinitionAlreadyRegistered::class)]
final class EntityDefinitionAlreadyRegisteredTest extends Framework\TestCase
{
    public function testForReturnsException(): void
    {
        $className = self::class;

        $exception = Exception\EntityDefinitionAlreadyRegistered::for($className);

        $message = \sprintf(
            'An entity definition for class name "%s" has already been registered.',
            $className,
        );

        self::assertInstanceOf(Exception\EntityDefinitionAlreadyRegistered::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
