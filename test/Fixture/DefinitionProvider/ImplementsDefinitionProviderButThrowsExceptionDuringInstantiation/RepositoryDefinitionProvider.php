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

namespace Ergebnis\FactoryBot\Test\Fixture\DefinitionProvider\ImplementsDefinitionProviderButThrowsExceptionDuringInstantiation;

use Ergebnis\FactoryBot\EntityDefinitionProvider;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

final class RepositoryDefinitionProvider implements EntityDefinitionProvider
{
    public function __construct()
    {
        throw new \RuntimeException();
    }

    public function accept(FixtureFactory $fixtureFactory): void
    {
        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class);
    }
}
