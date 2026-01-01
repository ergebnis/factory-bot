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

namespace Ergebnis\FactoryBot;

final class Count
{
    private function __construct(
        private int $minimum,
        private int $maximum,
    ) {
    }

    /**
     * @throws Exception\InvalidCount
     */
    public static function exact(int $value): self
    {
        if (0 > $value) {
            throw Exception\InvalidCount::notGreaterThanOrEqualToZero($value);
        }

        return new self(
            $value,
            $value,
        );
    }

    /**
     * @throws Exception\InvalidMaximum
     * @throws Exception\InvalidMinimum
     */
    public static function between(
        int $minimum,
        int $maximum,
    ): self {
        if (0 > $minimum) {
            throw Exception\InvalidMinimum::notGreaterThanOrEqualToZero($minimum);
        }

        if ($maximum <= $minimum) {
            throw Exception\InvalidMaximum::notGreaterThanMinimum(
                $minimum,
                $maximum,
            );
        }

        return new self(
            $minimum,
            $maximum,
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
