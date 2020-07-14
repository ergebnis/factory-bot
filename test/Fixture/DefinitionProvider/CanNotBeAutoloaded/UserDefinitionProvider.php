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

namespace Ergebnis\FactoryBot\Test\Fixture\DefinitionProvider\CanNotBeAutoloaded;

use Ergebnis\FactoryBot\EntityDefinitionProvider;
use Ergebnis\FactoryBot\FixtureFactory;
use Example\Entity;

final class UserDefinitionProvider implements EntityDefinitionProvider
{
    public function accept(FixtureFactory $fixtureFactory): void
    {
        $fixtureFactory->define(Entity\User::class);
    }
}
