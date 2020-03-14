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
use Doctrine\ORM;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDef;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDef
 * @uses \Ergebnis\FactoryBot\Exception\EntityDefinitionUnavailable
 * @uses \Ergebnis\FactoryBot\FieldDef
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testGetThrowsEntityDefinitionUnavailableWhenDefinitionIsUnavailable(): void
    {
        $entityManager = $this->prophesize(ORM\EntityManagerInterface::class)->reveal();

        $fixtureFactory = new FixtureFactory($entityManager);

        $this->expectException(Exception\EntityDefinitionUnavailable::class);

        $fixtureFactory->get('foo');
    }

    public function testThrowsWhenTryingToDefineTheSameEntityTwice(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
    }

    public function testThrowsWhenTryingToDefineEntitiesThatAreNotEvenClasses(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity('NotAClass');
    }

    public function testThrowsWhenTryingToDefineEntitiesThatAreNotEntities(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        self::assertTrue(\class_exists(Fixture\FixtureFactory\NotAnEntity\User::class, true));

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\NotAnEntity\User::class);
    }

    public function testThrowsWhenTryingToDefineNonexistentFields(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'pieType' => 'blueberry',
        ]);
    }

    public function testThrowsWhenTryingToGiveNonexistentFieldsWhileConstructing(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, ['name' => 'Alpha']);

        $this->expectException(\Exception::class);

        $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'pieType' => 'blueberry',
        ]);
    }

    public function testThrowsWhenTryingToGetLessThanOneInstance(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->getList(Fixture\FixtureFactory\Entity\Spaceship::class, [], 0);
    }

    public function testAcceptsConstantValuesInEntityDefinitions(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'My BattleCruiser',
        ]);

        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('My BattleCruiser', $ss->getName());
    }

    public function testAcceptsGeneratorFunctionsInEntityDefinitions(): void
    {
        $name = 'Star';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => static function () use (&$name) {
                return "M/S {$name}";
            },
        ]);

        self::assertSame('M/S Star', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        $name = 'Superstar';
        self::assertSame('M/S Superstar', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
    }

    public function testValuesCanBeOverriddenAtCreationTime(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'My BattleCruiser',
        ]);

        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, ['name' => 'My CattleBruiser']);

        self::assertSame('My CattleBruiser', $ss->getName());
    }

    public function testPreservesDefaultValuesOfEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\SpaceStation::class);

        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\SpaceStation::class);

        self::assertSame('Babylon5', $ss->getName());
    }

    public function testDoesNotCallTheConstructorOfTheEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, []);

        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertFalse($ss->constructorWasCalled());
    }

    public function testInstantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Battlestar Galaxy',
        ]);

        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $ss->getCrew());
        self::assertEmpty($ss->getCrew());
    }

    public function testArrayElementsAreMappedToCollectionAsscociationFields(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        $p1 = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);
        $p2 = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        $ship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, [
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

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        self::assertNull($fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
    }

    public function testEntityIsDefinedToDefaultNamespace(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person\User::class);

        self::assertInstanceOf(
            Fixture\FixtureFactory\Entity\Spaceship::class,
            $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)
        );

        self::assertInstanceOf(
            Fixture\FixtureFactory\Entity\Person\User::class,
            $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person\User::class)
        );
    }

    public function testEntityCanBeDefinedToAnotherNamespace(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(
            Fixture\FixtureFactory\Entity\Artist::class
        );

        self::assertInstanceOf(
            Fixture\FixtureFactory\Entity\Artist::class,
            $fixtureFactory->get(
                Fixture\FixtureFactory\Entity\Artist::class
            )
        );
    }

    public function testReturnsListOfEntities(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertCount(1, $fixtureFactory->getList(Fixture\FixtureFactory\Entity\Spaceship::class));
    }

    public function testCanSpecifyNumberOfReturnedInstances(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertCount(5, $fixtureFactory->getList(Fixture\FixtureFactory\Entity\Spaceship::class, [], 5));
    }

    public function testBidirectionalOntToManyReferencesAreAssignedBothWays(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        $person = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);
        $ship = $person->getSpaceShip();

        self::assertContains($person, $ship->getCrew());
    }

    public function testUnidirectionalReferencesWorkAsUsual(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Badge::class, [
            'owner' => FieldDef::reference(Fixture\FixtureFactory\Entity\Person::class),
        ]);
        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Person::class, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Badge::class)->getOwner());
    }

    public function testReferencedObjectsShouldBeCreatedAutomatically(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'crew' => FieldDef::references(Fixture\FixtureFactory\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceShip */
        $spaceShip = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        $crew = $spaceShip->getCrew();

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $crew);
        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Person::class, $crew);
        self::assertCount(1, $crew);
    }

    public function testReferencedObjectsShouldBeOverrideable(): void
    {
        $count = 5;

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'crew' => FieldDef::references(Fixture\FixtureFactory\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceShip */
        $spaceShip = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'crew' => $fixtureFactory->getList(Fixture\FixtureFactory\Entity\Person::class, [], $count),
        ]);

        $crew = $spaceShip->getCrew();

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $crew);
        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Person::class, $crew);
        self::assertCount($count, $crew);
    }

    public function testReferencedObjectsShouldBeNullable(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'crew' => FieldDef::references(Fixture\FixtureFactory\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceShip */
        $spaceShip = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'crew' => null,
        ]);

        $crew = $spaceShip->getCrew();

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $crew);
        self::assertEmpty($crew);
    }

    public function testReferencedObjectsShouldBeNullableVariation(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'name' => 'Eve',
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        $person = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class, ['spaceship' => null]);

        self::assertNull($person->getSpaceShip());
    }

    public function testAfterGettingAnEntityAsASingletonGettingTheEntityAgainReturnsTheSameObject(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $ss = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame($ss, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
        self::assertSame($ss, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
    }

    public function testGetAsSingletonMethodAcceptsFieldOverridesLikeGet(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $ss = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, ['name' => 'Beta']);
        self::assertSame('Beta', $ss->getName());
        self::assertSame('Beta', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
    }

    public function testThrowsAnErrorWhenCallingGetSingletonTwiceOnTheSameEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, ['name' => 'Alpha']);
        $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);
    }

    //TODO: should it be an error to get() a singleton with overrides?

    public function testAllowsSettingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $ss = new Fixture\FixtureFactory\Entity\Spaceship('The mothership');

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, $ss);

        self::assertSame($ss, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
    }

    public function testAllowsUnsettingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $ss = new Fixture\FixtureFactory\Entity\Spaceship('The mothership');

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, $ss);
        $fixtureFactory->unsetSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertNotSame($ss, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
    }

    public function testAllowsOverwritingExistingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $ss1 = new Fixture\FixtureFactory\Entity\Spaceship('The mothership');
        $ss2 = new Fixture\FixtureFactory\Entity\Spaceship('The battlecruiser');

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, $ss1);
        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, $ss2);

        self::assertSame($ss2, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
    }

    public function testReferencedObjectsCanBeSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'crew' => FieldDef::references(Fixture\FixtureFactory\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Fixture\FixtureFactory\Entity\Person $person */
        $person = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Person::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceShip */
        $spaceShip = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        $crew = $spaceShip->getCrew();

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $crew);
        self::assertContains($person, $crew);
        self::assertCount(1, $crew);
    }

    public function testWhenTheOneSideIsASingletonItMayGetSeveralChildObjects(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        $ship = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);
        $p1 = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);
        $p2 = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        self::assertContains($p1, $ship->getCrew());
        self::assertContains($p2, $ship->getCrew());
    }

    public function testCanInvokeACallbackAfterObjectConstruction(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Foo',
        ], [
            'afterCreate' => static function (Fixture\FixtureFactory\Entity\Spaceship $ss, array $fieldValues): void {
                $ss->setName($ss->getName() . '-' . $fieldValues['name']);
            },
        ]);
        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('Foo-Foo', $ss->getName());
    }

    public function testTheAfterCreateCallbackCanBeUsedToCallTheConstructor(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Foo',
        ], [
            'afterCreate' => static function (Fixture\FixtureFactory\Entity\Spaceship $ss, array $fieldValues): void {
                $ss->__construct($fieldValues['name'] . 'Master');
            },
        ]);
        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, ['name' => 'Xoo']);

        self::assertTrue($ss->constructorWasCalled());
        self::assertSame('XooMaster', $ss->getName());
    }

    public function testReferencedObjectShouldBeCreatedAutomatically(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'name' => 'Eve',
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        $ss1 = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class)->getSpaceShip();
        $ss2 = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class)->getSpaceShip();

        self::assertNotNull($ss1);
        self::assertNotNull($ss2);
        self::assertNotSame($ss1, $ss2);
    }

    public function testReferencesGetInstantiatedTransitively(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Badge::class, [
            'owner' => FieldDef::reference(Fixture\FixtureFactory\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $badge = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Badge::class);

        self::assertNotNull($badge->getOwner()->getSpaceShip());
    }

    public function testTransitiveReferencesWorkWithSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Badge::class, [
            'owner' => FieldDef::reference(Fixture\FixtureFactory\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);
        $badge1 = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Badge::class);
        $badge2 = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Badge::class);

        self::assertNotSame($badge1->getOwner(), $badge2->getOwner());
        self::assertSame($badge1->getOwner()->getSpaceShip(), $badge2->getOwner()->getSpaceShip());
    }

    public function testSequenceGeneratorCallsAFunctionWithAnIncrementingArgument(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => FieldDef::sequence(static function ($n) {
                return "Alpha {$n}";
            }),
        ]);
        self::assertSame('Alpha 1', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Alpha 2', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Alpha 3', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Alpha 4', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
    }

    public function testSequenceGeneratorCanTakeAPlaceholderString(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => FieldDef::sequence('Beta %d'),
        ]);
        self::assertSame('Beta 1', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Beta 2', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Beta 3', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Beta 4', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
    }

    public function testSequenceGeneratorCanTakeAStringToAppendTo(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => FieldDef::sequence('Gamma '),
        ]);
        self::assertSame('Gamma 1', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Gamma 2', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Gamma 3', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
        self::assertSame('Gamma 4', $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class)->getName());
    }
}
