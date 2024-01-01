<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Exception;

final class InvalidCount extends \InvalidArgumentException implements Exception
{
    public static function notGreaterThanOrEqualToZero(int $count): self
    {
        return new self(\sprintf(
            'Count needs to be greater than or equal to 0, but %d is not.',
            $count,
        ));
    }
}
