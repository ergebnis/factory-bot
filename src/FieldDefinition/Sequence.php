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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

/**
 * @internal
 */
final class Sequence implements Resolvable
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $sequentialNumber;

    /**
     * @throws Exception\InvalidSequence
     */
    public function __construct(string $value, int $initialNumber)
    {
        if (false === \strpos($value, '%d')) {
            throw Exception\InvalidSequence::value($value);
        }

        $this->value = $value;
        $this->sequentialNumber = $initialNumber;
    }

    public function resolve(Generator $faker, FixtureFactory $fixtureFactory): string
    {
        return \str_replace(
            '%d',
            (string) $this->sequentialNumber++,
            $this->value
        );
    }
}
