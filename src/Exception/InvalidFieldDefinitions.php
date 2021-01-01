<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Exception;

use Ergebnis\FactoryBot\FieldDefinition;

final class InvalidFieldDefinitions extends \InvalidArgumentException implements Exception
{
    public static function values(): self
    {
        return new self(\sprintf(
            'Field definitions need to be instances of "%s".',
            FieldDefinition::class
        ));
    }
}
