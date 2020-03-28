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

namespace Ergebnis\FactoryBot\Test\Fixture\Definition\Definitions\DoesNotImplementInterface;

use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * Is not acceptable as it does not implement the DefinitionInterface.
 */
final class RepositoryDefinition
{
    public function accept(FixtureFactory $factory): void
    {
        $factory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class);
    }
}
