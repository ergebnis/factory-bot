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

namespace Ergebnis\FactoryBot\Test\Unit;

use Doctrine\ORM;
use Ergebnis\FactoryBot\EntityDefinitionUnavailable;
use Ergebnis\FactoryBot\FixtureFactory;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinitionUnavailable
 */
final class FixtureFactoryTest extends Framework\TestCase
{
    public function testGetThrowsEntityDefinitionUnavailableWhenDefinitionIsUnavailable(): void
    {
        $entityManager = $this->prophesize(ORM\EntityManager::class)->reveal();

        $fixtureFactory = new FixtureFactory($entityManager);

        $this->expectException(EntityDefinitionUnavailable::class);

        $fixtureFactory->get('foo');
    }
}
