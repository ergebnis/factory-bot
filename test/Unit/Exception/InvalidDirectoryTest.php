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

#[Framework\Attributes\CoversClass(FactoryBot\Exception\InvalidDirectory::class)]
final class InvalidDirectoryTest extends Framework\TestCase
{
    use FactoryBot\Test\Util\Helper;

    public function testNotDirectoryCreatesException(): void
    {
        $directory = self::faker()->word();

        $exception = FactoryBot\Exception\InvalidDirectory::notDirectory($directory);

        $message = \sprintf(
            'Directory should be a directory, but "%s" is not.',
            $directory,
        );

        self::assertInstanceOf(FactoryBot\Exception\InvalidDirectory::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(FactoryBot\Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
