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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\FixtureFactory;

/**
 * @internal
 */
final class Value implements Resolvable
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function resolve(FixtureFactory $fixtureFactory)
    {
        return $this->value;
    }
}
