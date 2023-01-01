<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Fixture\DefinitionProvider\ImplementsDefinitionProviderButIsAbstract;

use Ergebnis\FactoryBot;
use Example\Entity;

abstract class RepositoryDefinitionProvider implements FactoryBot\EntityDefinitionProvider
{
    final public function accept(FactoryBot\FixtureFactory $fixtureFactory): void
    {
        $fixtureFactory->define(Entity\Repository::class);
    }
}
