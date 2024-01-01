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

final class InvalidSequence extends \InvalidArgumentException implements Exception
{
    public static function value(string $value): self
    {
        return new self(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        ));
    }
}
