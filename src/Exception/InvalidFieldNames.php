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

final class InvalidFieldNames extends \InvalidArgumentException implements Exception
{
    public static function notFoundIn(
        string $className,
        string ...$fieldNames,
    ): self {
        \natsort($fieldNames);

        $template = 'Entity "%s" does not have fields with the names "%s".';

        if (1 === \count($fieldNames)) {
            $template = 'Entity "%s" does not have a field with the name "%s".';
        }

        return new self(\sprintf(
            $template,
            $className,
            \implode('", "', $fieldNames),
        ));
    }
}
