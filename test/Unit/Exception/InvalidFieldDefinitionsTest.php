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
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Exception\InvalidFieldDefinitions::class)]
final class InvalidFieldDefinitionsTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testValuesCreatesException(): void
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
