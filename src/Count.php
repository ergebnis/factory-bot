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

namespace Ergebnis\FactoryBot;

final class Count
{
    /**
     * @var int
     */
    private $minimum;

    /**
     * @var int
     */
    private $maximum;

    private function __construct(int $minimum, int $maximum)
    {
        $this->minimum = $minimum;
        $this->maximum = $maximum;
    }

    /**
     * @param int $value
     *
     * @throws Exception\InvalidCount
     *
     * @return self
     */
    public static function exact(int $value): self
    {
        if (0 > $value) {
            throw Exception\InvalidCount::notGreaterThanOrEqualToZero($value);
        }

        return new self(
            $value,
            $value
        );
    }

    /**
     * @param int $minimum
     * @param int $maximum
     *
     * @throws Exception\InvalidMaximum
     * @throws Exception\InvalidMinimum
     *
     * @return self
     */
    public static function between(int $minimum, int $maximum): self
    {
        if (0 > $minimum) {
            throw Exception\InvalidMinimum::notGreaterThanOrEqualToZero($minimum);
        }

        if ($maximum <= $minimum) {
            throw Exception\InvalidMaximum::notGreaterThanMinimum(
                $minimum,
                $maximum
            );
        }

        return new self(
            $minimum,
            $maximum
        );
    }

    public function minimum(): int
    {
        return $this->minimum;
    }

    public function maximum(): int
    {
        return $this->maximum;
    }
}
