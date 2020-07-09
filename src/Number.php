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

final class Number
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     *
     * @throws Exception\InvalidNumber
     */
    public function __construct(int $value)
    {
        if (0 > $value) {
            throw Exception\InvalidNumber::notGreaterThanOrEqualToZero($value);
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }
}
