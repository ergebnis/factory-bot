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

namespace Ergebnis\FactoryBot\Exception;

final class InvalidMaximum extends \InvalidArgumentException implements Exception
{
    public static function notGreaterThanMinimum(
        int $minimum,
        int $maximum,
    ): self {
        return new self(\sprintf(
            'Maximum needs to be greater than minimum %d, but %d is not.',
            $minimum,
            $maximum,
        ));
    }
}
