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

namespace Ergebnis\FactoryBot\Test\DataProvider;

use Ergebnis\Test\Util\Helper;

final class NumberProvider
{
    use Helper;

    /**
     * @return \Generator<string, array<int>>
     */
    public function intLessThanOne(): \Generator
    {
        $values = [
            'int-zero' => 0,
            'int-minus-one' => -1,
            'int-less-than-minus-one' => -1 * self::faker()->numberBetween(2),
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
    public function intGreaterThanOrEqualToOne(): \Generator
    {
        $values = [
            'int-one' => 1,
            'int-two' => 2,
            'int-greater-than-two' => self::faker()->numberBetween(3, 10),
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
