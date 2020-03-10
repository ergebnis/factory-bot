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

use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDef
 */
final class SingletonTest extends AbstractTestCase
{
    public function testAfterGettingAnEntityAsASingletonGettingTheEntityAgainReturnsTheSameObject(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);

        $ss = $fixtureFactory->getAsSingleton(Fixture\Entity\SpaceShip::class);

        self::assertSame($ss, $fixtureFactory->get(Fixture\Entity\SpaceShip::class));
        self::assertSame($ss, $fixtureFactory->get(Fixture\Entity\SpaceShip::class));
    }

    public function testGetAsSingletonMethodAcceptsFieldOverridesLikeGet(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);

        $ss = $fixtureFactory->getAsSingleton(Fixture\Entity\SpaceShip::class, ['name' => 'Beta']);
        self::assertSame('Beta', $ss->getName());
        self::assertSame('Beta', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
    }

    public function testThrowsAnErrorWhenCallingGetSingletonTwiceOnTheSameEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, ['name' => 'Alpha']);
        $fixtureFactory->getAsSingleton(Fixture\Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->getAsSingleton(Fixture\Entity\SpaceShip::class);
    }

    //TODO: should it be an error to get() a singleton with overrides?

    public function testAllowsSettingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $ss = new Fixture\Entity\SpaceShip('The mothership');

        $fixtureFactory->setSingleton(Fixture\Entity\SpaceShip::class, $ss);

        self::assertSame($ss, $fixtureFactory->get(Fixture\Entity\SpaceShip::class));
    }

    public function testAllowsUnsettingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $ss = new Fixture\Entity\SpaceShip('The mothership');

        $fixtureFactory->setSingleton(Fixture\Entity\SpaceShip::class, $ss);
        $fixtureFactory->unsetSingleton(Fixture\Entity\SpaceShip::class);

        self::assertNotSame($ss, $fixtureFactory->get(Fixture\Entity\SpaceShip::class));
    }

    public function testAllowsOverwritingExistingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $ss1 = new Fixture\Entity\SpaceShip('The mothership');
        $ss2 = new Fixture\Entity\SpaceShip('The battlecruiser');

        $fixtureFactory->setSingleton(Fixture\Entity\SpaceShip::class, $ss1);
        $fixtureFactory->setSingleton(Fixture\Entity\SpaceShip::class, $ss2);

        self::assertSame($ss2, $fixtureFactory->get(Fixture\Entity\SpaceShip::class));
    }
}
