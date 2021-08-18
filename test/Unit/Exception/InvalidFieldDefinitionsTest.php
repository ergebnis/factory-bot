<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit\Exception;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Exception\InvalidFieldDefinitions
 */
final class InvalidFieldDefinitionsTest extends Framework\TestCase
{
    use Helper;

    public function testFromClassNameCreatesException(): void
    {
        $exception = Exception\InvalidFieldDefinitions::values();

        $message = \sprintf(
            'Field definitions need to be instances of "%s".',
            FieldDefinition::class,
        );

        self::assertInstanceOf(Exception\InvalidFieldDefinitions::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
