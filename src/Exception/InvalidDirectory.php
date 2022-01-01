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

final class InvalidDirectory extends \InvalidArgumentException implements Exception
{
    public static function notDirectory(string $directory): self
    {
        return new self(\sprintf(
            'Directory should be a directory, but "%s" is not.',
            $directory,
        ));
    }
}
