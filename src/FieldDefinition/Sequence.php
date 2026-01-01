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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

/**
 * @internal
 */
final class Sequence implements Resolvable
{
    private int $sequentialNumber;

    /**
     * @throws Exception\InvalidSequence
     */
    public function __construct(
        private string $value,
        int $initialNumber,
    ) {
        if (!\str_contains($value, '%d')) {
            throw Exception\InvalidSequence::value($value);
        }

        $this->sequentialNumber = $initialNumber;
    }

    public function resolve(
        Generator $faker,
        FixtureFactory $fixtureFactory,
    ): string {
        return \str_replace(
            '%d',
            (string) $this->sequentialNumber++,
            $this->value,
        );
    }
}
