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

final class ClassMetadataNotFound extends \RuntimeException implements Exception
{
    public static function for(string $className): self
    {
        return new self(\sprintf(
            'Class metadata for a class with the name "%s" could not be found.',
            $className
        ));
    }
}
