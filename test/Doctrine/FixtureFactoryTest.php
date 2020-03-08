<?php

declare(strict_types=1);

namespace FactoryGirl\Tests\Provider\Doctrine;

use Doctrine\ORM\EntityManager;
use FactoryGirl\Provider\Doctrine\EntityDefinitionUnavailable;
use FactoryGirl\Provider\Doctrine\FixtureFactory;
use PHPUnit\Framework;

/**
 * @covers \FactoryGirl\Provider\Doctrine\FixtureFactory
 *
 * @uses \FactoryGirl\Provider\Doctrine\EntityDefinitionUnavailable
 */
final class FixtureFactoryTest extends Framework\TestCase
{
    public function testGetThrowsEntityDefinitionUnavailableWhenDefinitionIsUnavailable(): void
    {
        $entityManager = $this->prophesize(EntityManager::class)->reveal();

        $fixtureFactory = new FixtureFactory($entityManager);

        $this->expectException(EntityDefinitionUnavailable::class);

        $fixtureFactory->get('foo');
    }
}
