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

namespace Ergebnis\FactoryGirl\Definition\Test\Fixture\Definition\ExtendsAbstractDefinition;

use Ergebnis\FactoryGirl\Definition\AbstractDefinition;
use Ergebnis\FactoryGirl\Definition\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

final class UserDefinition extends AbstractDefinition
{
    public function accept(FixtureFactory $factory): void
    {
        $factory->defineEntity(Entity\User::class);
    }
}
