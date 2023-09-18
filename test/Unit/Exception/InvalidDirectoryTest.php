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
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

#[\PHPUnit\Framework\Attributes\CoversClass(\Ergebnis\FactoryBot\Exception\InvalidDirectory::class)]
final class InvalidDirectoryTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNotDirectoryCreatesException(): void
    {
        $directory = self::faker()->word();

        $exception = Exception\InvalidDirectory::notDirectory($directory);

        $message = \sprintf(
            'Directory should be a directory, but "%s" is not.',
            $directory,
        );

        self::assertInstanceOf(Exception\InvalidDirectory::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
    }
}
