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

namespace Ergebnis\FactoryBot\Test\DataProvider;

use Ergebnis\FactoryBot;

final class IntProvider
{
    use FactoryBot\Test\Util\Helper;

    /**
     * @return \Generator<string, list<int>>
     */
    public static function arbitrary(): \Generator
    {
        $faker = self::faker();

        $values = [
            'int-greater-than-one' => $faker->numberBetween(2),
            'int-less-than-minus-one' => -1 * $faker->numberBetween(2),
            'int-minus-one' => -1,
            'int-one' => 1,
            'int-zero' => 0,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @return \Generator<string, list<int>>
     */
    public static function lessThanZero(): \Generator
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
     * @return \Generator<string, list<int>>
     */
    public static function greaterThanOrEqualToZero(): \Generator
    {
        $values = [
            'int-greater-than-one' => self::faker()->numberBetween(2, 10),
            'int-one' => 1,
            'int-zero' => 0,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @return \Generator<string, list<int>>
     */
    public static function greaterThanZero(): \Generator
    {
        $values = [
            'int-greater-than-one' => self::faker()->numberBetween(2, 10),
            'int-one' => 1,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
