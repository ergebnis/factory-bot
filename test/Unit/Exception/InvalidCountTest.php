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
 * @covers \Ergebnis\FactoryBot\Exception\InvalidCount
 */
final class InvalidCountTest extends Framework\TestCase
{
    use Helper;

    public function testNotGreaterThanOrEqualToReturnsException(): void
    {
        $faker = self::faker();

        $minimumCount = $faker->numberBetween(1, 1000);
        $count = $minimumCount - $faker->numberBetween(1);

        $exception = Exception\InvalidCount::notGreaterThanOrEqualTo(
            $minimumCount,
            $count
        );

        $message = \sprintf(
            'Count needs to be greater than or equal to %d, but %d is not.',
            $minimumCount,
            $count
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
