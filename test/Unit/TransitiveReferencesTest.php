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

use Ergebnis\FactoryBot\FieldDef;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDef
 * @uses \Ergebnis\FactoryBot\FieldDef
 */
final class TransitiveReferencesTest extends AbstractTestCase
{
    public function testReferencesGetInstantiatedTransitively(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Fixture\Entity\SpaceShip::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\Badge::class, [
            'owner' => FieldDef::reference(Fixture\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);

        $badge = $fixtureFactory->get(Fixture\Entity\Badge::class);

        self::assertNotNull($badge->getOwner()->getSpaceShip());
    }

    public function testTransitiveReferencesWorkWithSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Fixture\Entity\SpaceShip::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\Badge::class, [
            'owner' => FieldDef::reference(Fixture\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);

        $fixtureFactory->getAsSingleton(Fixture\Entity\SpaceShip::class);
        $badge1 = $fixtureFactory->get(Fixture\Entity\Badge::class);
        $badge2 = $fixtureFactory->get(Fixture\Entity\Badge::class);

        self::assertNotSame($badge1->getOwner(), $badge2->getOwner());
        self::assertSame($badge1->getOwner()->getSpaceShip(), $badge2->getOwner()->getSpaceShip());
    }
}
