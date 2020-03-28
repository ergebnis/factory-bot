<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020 Andreas Möller
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
 * @covers \Ergebnis\FactoryBot\Exception\EntityDefinitionUnavailable
 */
final class EntityDefinitionUnavailableTest extends Framework\TestCase
{
    public function testForReturnsException(): void
    {
        $className = 'foo';

        $exception = Exception\EntityDefinitionUnavailable::for($className);

        self::assertInstanceOf(\OutOfRangeException::class, $exception);

        $message = \sprintf(
            'An entity definition for class name "%s" is not available.',
            $className
        );

        self::assertSame($message, $exception->getMessage());
    }
}
