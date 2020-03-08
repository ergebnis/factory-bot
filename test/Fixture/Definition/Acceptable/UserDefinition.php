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

namespace Ergebnis\FactoryGirl\Definition\Test\Fixture\Definition\Acceptable;

use Ergebnis\FactoryGirl\Definition\Definition;
use Ergebnis\FactoryGirl\Definition\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

/**
 * Is acceptable as it implements the interface.
 */
final class UserDefinition implements Definition
{
    public function accept(FixtureFactory $factory): void
    {
        $factory->defineEntity(Entity\User::class);
    }
}
