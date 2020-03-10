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
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Exception\InvalidDirectory
 */
final class InvalidDirectoryTest extends Framework\TestCase
{
    use Helper;

    public function testNotDirectoryCreatesException(): void
    {
        $directory = self::faker()->word;

        $exception = Exception\InvalidDirectory::notDirectory($directory);

        $message = \sprintf(
            'Directory should be a directory, but "%s" is not.',
            $directory
        );

        self::assertSame($message, $exception->getMessage());
    }
}
