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
 * @covers \Ergebnis\FactoryBot\Exception\InvalidNumber
 */
final class InvalidNumberTest extends Framework\TestCase
{
    use Helper;

    public function testNotGreaterThanOrEqualToReturnsException(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1, 1000);
        $number = $minimum - $faker->numberBetween(1);

        $exception = Exception\InvalidNumber::notGreaterThanOrEqualTo(
            $minimum,
            $number
        );

        $message = \sprintf(
            'Number needs to be greater than or equal to %d, but %d is not.',
            $minimum,
            $number
        );

        self::assertInstanceOf(Exception\InvalidNumber::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
