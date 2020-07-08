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

namespace Ergebnis\FactoryBot\Exception;

final class InvalidNumber extends \InvalidArgumentException implements Exception
{
    public static function notGreaterThanOrEqualToZero(int $number): self
    {
        return new self(\sprintf(
            'Number needs to be greater than or equal to 0, but %d is not.',
            $number
        ));
    }
}
