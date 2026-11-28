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

#[Framework\Attributes\CoversClass(FactoryBot\Exception\InvalidFieldDefinitions::class)]
final class InvalidFieldDefinitionsTest extends Framework\TestCase
{
    use FactoryBot\Test\Util\Helper;

    public function testValuesCreatesException(): void
    {
        $exception = FactoryBot\Exception\InvalidFieldDefinitions::values();

        $message = \sprintf(
            'Field definitions need to be instances of "%s".',
            FactoryBot\FieldDefinition::class,
        );

        self::assertInstanceOf(FactoryBot\Exception\InvalidFieldDefinitions::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
