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

final class EntityDefinitionUnavailable extends \OutOfRangeException implements Exception
{
    public static function for(string $name): self
    {
        return new self(\sprintf(
            'An entity definition for name "%s" is not available.',
            $name
        ));
    }
}
