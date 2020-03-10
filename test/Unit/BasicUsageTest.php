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

use Doctrine\Common;
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
final class BasicUsageTest extends AbstractTestCase
{
    public function testAcceptsConstantValuesInEntityDefinitions(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Fixture\Entity\SpaceShip::class, [
                'name' => 'My BattleCruiser',
            ])
            ->get(Fixture\Entity\SpaceShip::class);

        self::assertSame('My BattleCruiser', $ss->getName());
    }

    public function testAcceptsGeneratorFunctionsInEntityDefinitions(): void
    {
        $name = 'Star';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'name' => static function () use (&$name) {
                return "M/S {$name}";
            },
        ]);

        self::assertSame('M/S Star', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        $name = 'Superstar';
        self::assertSame('M/S Superstar', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
    }

    public function testValuesCanBeOverriddenAtCreationTime(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Fixture\Entity\SpaceShip::class, [
                'name' => 'My BattleCruiser',
            ])
            ->get(Fixture\Entity\SpaceShip::class, ['name' => 'My CattleBruiser']);
        self::assertSame('My CattleBruiser', $ss->getName());
    }

    public function testPreservesDefaultValuesOfEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Fixture\Entity\SpaceStation::class)
            ->get(Fixture\Entity\SpaceStation::class);
        self::assertSame('Babylon5', $ss->getName());
    }

    public function testDoesNotCallTheConstructorOfTheEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Fixture\Entity\SpaceShip::class, [])
            ->get(Fixture\Entity\SpaceShip::class);
        self::assertFalse($ss->constructorWasCalled());
    }

    public function testInstantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Fixture\Entity\SpaceShip::class, [
                'name' => 'Battlestar Galaxy',
            ])
            ->get(Fixture\Entity\SpaceShip::class);

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $ss->getCrew());
        self::assertEmpty($ss->getCrew());
    }

    public function testArrayElementsAreMappedToCollectionAsscociationFields(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Fixture\Entity\SpaceShip::class),
        ]);

        $p1 = $fixtureFactory->get(Fixture\Entity\Person::class);
        $p2 = $fixtureFactory->get(Fixture\Entity\Person::class);

        $ship = $fixtureFactory->get(Fixture\Entity\SpaceShip::class, [
            'name' => 'Battlestar Galaxy',
            'crew' => [$p1, $p2],
        ]);

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $ship->getCrew());
        self::assertTrue($ship->getCrew()->contains($p1));
        self::assertTrue($ship->getCrew()->contains($p2));
    }

    public function testUnspecifiedFieldsAreLeftNull(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        self::assertNull($fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
    }

    public function testEntityIsDefinedToDefaultNamespace(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Fixture\Entity\Person\User::class);

        self::assertInstanceOf(
            Fixture\Entity\SpaceShip::class,
            $fixtureFactory->get(Fixture\Entity\SpaceShip::class)
        );

        self::assertInstanceOf(
            Fixture\Entity\Person\User::class,
            $fixtureFactory->get(Fixture\Entity\Person\User::class)
        );
    }

    public function testEntityCanBeDefinedToAnotherNamespace(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(
            Fixture\Entity\Artist::class
        );

        self::assertInstanceOf(
            Fixture\Entity\Artist::class,
            $fixtureFactory->get(
                Fixture\Entity\Artist::class
            )
        );
    }

    public function testReturnsListOfEntities(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);

        self::assertCount(1, $fixtureFactory->getList(Fixture\Entity\SpaceShip::class));
    }

    public function testCanSpecifyNumberOfReturnedInstances(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);

        self::assertCount(5, $fixtureFactory->getList(Fixture\Entity\SpaceShip::class, [], 5));
    }
}
