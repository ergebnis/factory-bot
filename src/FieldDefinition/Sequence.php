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
     * @var bool
     */
    private $isRequired;

    private function __construct(string $value, int $initialNumber, bool $isRequired)
    {
        $this->value = $value;
        $this->sequentialNumber = $initialNumber;
        $this->isRequired = $isRequired;
    }

    /**
     * @param string $value
     * @param int    $initialNumber
     *
     * @throws Exception\InvalidSequence
     *
     * @return self
     */
    public static function required(string $value, int $initialNumber): self
    {
        if (false === \strpos($value, '%d')) {
            throw Exception\InvalidSequence::value($value);
        }

        return new self(
            $value,
            $initialNumber,
            true
        );
    }

    /**
     * @param string $value
     * @param int    $initialNumber
     *
     * @throws Exception\InvalidSequence
     *
     * @return self
     */
    public static function optional(string $value, int $initialNumber): self
    {
        if (false === \strpos($value, '%d')) {
            throw Exception\InvalidSequence::value($value);
        }

        return new self(
            $value,
            $initialNumber,
            false
        );
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function resolve(FixtureFactory $fixtureFactory): string
    {
        return \str_replace(
            '%d',
            (string) $this->sequentialNumber++,
            $this->value
        );
    }
}
