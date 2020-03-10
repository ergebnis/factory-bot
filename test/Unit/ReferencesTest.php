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

use Doctrine\Common\Collections\ArrayCollection;
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
final class ReferencesTest extends AbstractTestCase
{
    public function testReferencedObjectsShouldBeCreatedAutomatically(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Fixture\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Fixture\Entity\SpaceShip $spaceShip */
        $spaceShip = $fixtureFactory->get(Fixture\Entity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        self::assertInstanceOf(ArrayCollection::class, $crew);
        self::assertContainsOnly(Fixture\Entity\Person::class, $crew);
        self::assertCount(1, $crew);
    }

    public function testReferencedObjectsShouldBeOverrideable(): void
    {
        $count = 5;

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Fixture\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Fixture\Entity\SpaceShip $spaceShip */
        $spaceShip = $fixtureFactory->get(Fixture\Entity\SpaceShip::class, [
            'crew' => $fixtureFactory->getList(Fixture\Entity\Person::class, [], $count),
        ]);

        $crew = $spaceShip->getCrew();

        self::assertInstanceOf(ArrayCollection::class, $crew);
        self::assertContainsOnly(Fixture\Entity\Person::class, $crew);
        self::assertCount($count, $crew);
    }

    public function testReferencedObjectsShouldBeNullable(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Fixture\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Fixture\Entity\SpaceShip $spaceShip */
        $spaceShip = $fixtureFactory->get(Fixture\Entity\SpaceShip::class, [
            'crew' => null,
        ]);

        $crew = $spaceShip->getCrew();

        self::assertInstanceOf(ArrayCollection::class, $crew);
        self::assertEmpty($crew);
    }

    public function testReferencedObjectsCanBeSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Fixture\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Fixture\Entity\Person $person */
        $person = $fixtureFactory->getAsSingleton(Fixture\Entity\Person::class);

        /** @var Fixture\Entity\SpaceShip $spaceShip */
        $spaceShip = $fixtureFactory->get(Fixture\Entity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        self::assertInstanceOf(ArrayCollection::class, $crew);
        self::assertContains($person, $crew);
        self::assertCount(1, $crew);
    }
}
