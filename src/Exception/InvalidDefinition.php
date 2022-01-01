<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Exception;

final class InvalidDefinition extends \RuntimeException implements Exception
{
    public static function canNotBeAutoloaded(string $className): self
    {
        return new self(\sprintf(
            'Definition "%s" can not be autoloaded.',
            $className,
        ));
    }

    public static function canNotBeInstantiated(string $className): self
    {
        return new self(\sprintf(
            'Definition "%s" can not be instantiated.',
            $className,
        ));
    }

    public static function throwsExceptionDuringInstantiation(
        string $className,
        \Exception $exception
    ): self {
        return new self(
            \sprintf(
                'An exception was thrown while trying to instantiate definition "%s".',
                $className,
            ),
            0,
            $exception,
        );
    }
}
