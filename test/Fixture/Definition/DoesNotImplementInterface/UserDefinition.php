<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-girl-definition
 */

namespace Ergebnis\FactoryGirl\Definition\Test\Fixture\Definition\DoesNotImplementInterface;

use Ergebnis\FactoryGirl\Definition\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

/**
 * Is not acceptable as it does not implement the DefinitionInterface.
 */
final class UserDefinition
{
    public function accept(FixtureFactory $factory): void
    {
        $factory->defineEntity(Entity\User::class);
    }
}
