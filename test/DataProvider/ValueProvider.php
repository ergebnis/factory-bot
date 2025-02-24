<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\DataProvider;

use Ergebnis\FactoryBot\Test;

final class ValueProvider
{
    use Test\Util\Helper;

    /**
     * @return \Generator<string, bool|float|int|list<string>|resource|\stdClass|string>
     */
    public static function arbitrary(): \Generator
    {
        $faker = self::faker();

        $values = [
            'array' => $faker->words(),
            'bool-false' => false,
            'bool-true' => true,
            'float' => $faker->randomFloat(),
            'int' => $faker->numberBetween(),
            'object' => new \stdClass(),
            'resource' => \fopen(__FILE__, 'rb'),
            'string' => $faker->sentence(),
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
