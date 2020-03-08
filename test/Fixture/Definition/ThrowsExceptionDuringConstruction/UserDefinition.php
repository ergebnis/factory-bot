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

namespace Ergebnis\FactoryBot\Test\Fixture\Definition\ThrowsExceptionDuringConstruction;

use Ergebnis\FactoryBot\Definition\Definition;
use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

/**
 * Is not acceptable as it throws an exception during construction.
 */
final class UserDefinition implements Definition
{
    public function __construct()
    {
        throw new \RuntimeException();
    }

    public function accept(FixtureFactory $factory): void
    {
        $factory->defineEntity(Entity\User::class);
    }
}
