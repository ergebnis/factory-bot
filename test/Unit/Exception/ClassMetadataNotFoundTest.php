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
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Exception\ClassMetadataNotFound
 */
final class ClassMetadataNotFoundTest extends Framework\TestCase
{
    public function testNameReturnsException(): void
    {
        $className = 'foo';

        $exception = Exception\ClassMetadataNotFound::for($className);

        $message = \sprintf(
            'Class metadata for a class with the name "%s" could not be found.',
            $className,
        );

        self::assertInstanceOf(Exception\ClassMetadataNotFound::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
