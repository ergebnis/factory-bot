<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\DataProvider;

use Ergebnis\Test\Util;

final class IntProvider
{
    use Util\Helper;

    /**
     * @return \Generator<string, array<int>>
     */
    public function arbitrary(): \Generator
    {
        $faker = self::faker();

        $values = [
            'int-less-than-minus-one' => -1 * $faker->numberBetween(2),
            'int-minus-one' => -1,
            'int-zero' => 0,
            'int-one' => 1,
            'int-greater-than-one' => $faker->numberBetween(2),
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @return \Generator<string, array<int>>
     */
    public function lessThanZero(): \Generator
    {
        $values = [
            'int-less-than-minus-one' => -1 * self::faker()->numberBetween(2),
            'int-minus-one' => -1,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @return \Generator<string, array<int>>
     */
    public function greaterThanOrEqualToZero(): \Generator
    {
        $values = [
            'int-zero' => 0,
            'int-one' => 1,
            'int-greater-than-one' => self::faker()->numberBetween(2, 10),
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @return \Generator<string, array<int>>
     */
    public function greaterThanZero()
    {
        $values = [
            'int-one' => 1,
            'int-greater-than-one' => self::faker()->numberBetween(2, 10),
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
