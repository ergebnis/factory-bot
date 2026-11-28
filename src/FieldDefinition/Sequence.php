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

use Ergebnis\FactoryBot;
use Faker\Generator;

/**
 * @internal
 */
final class Sequence implements Resolvable
{
    /**
     * @throws FactoryBot\Exception\InvalidSequence
     */
    public function __construct(
        private readonly string $value,
        private int $sequentialNumber,
    ) {
        if (!\str_contains($value, '%d')) {
            throw FactoryBot\Exception\InvalidSequence::value($value);
        }
    }

    public function resolve(
        Generator $faker,
        FactoryBot\FixtureFactory $fixtureFactory,
    ): string {
        return \str_replace(
            '%d',
            (string) $this->sequentialNumber++,
            $this->value,
        );
    }
}
