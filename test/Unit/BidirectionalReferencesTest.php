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
final class BidirectionalReferencesTest extends AbstractTestCase
{
    public function testBidirectionalOntToManyReferencesAreAssignedBothWays(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Fixture\Entity\SpaceShip::class),
        ]);

        $person = $fixtureFactory->get(Fixture\Entity\Person::class);
        $ship = $person->getSpaceShip();

        self::assertContains($person, $ship->getCrew());
    }

    public function testUnidirectionalReferencesWorkAsUsual(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\Badge::class, [
            'owner' => FieldDef::reference(Fixture\Entity\Person::class),
        ]);
        $fixtureFactory->defineEntity(Fixture\Entity\Person::class);

        self::assertInstanceOf(Fixture\Entity\Person::class, $fixtureFactory->get(Fixture\Entity\Badge::class)->getOwner());
    }

    public function testWhenTheOneSideIsASingletonItMayGetSeveralChildObjects(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Fixture\Entity\SpaceShip::class),
        ]);

        $ship = $fixtureFactory->getAsSingleton(Fixture\Entity\SpaceShip::class);
        $p1 = $fixtureFactory->get(Fixture\Entity\Person::class);
        $p2 = $fixtureFactory->get(Fixture\Entity\Person::class);

        self::assertContains($p1, $ship->getCrew());
        self::assertContains($p2, $ship->getCrew());
    }
}
