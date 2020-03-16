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
 * @covers \Ergebnis\FactoryBot\EntityDef
 * @covers \Ergebnis\FactoryBot\FieldDef
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\Exception\EntityDefinitionUnavailable
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testDefineEntityThrowsExceptionWhenDefinitionHasAlreadyBeenProvidedForEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Entity \'%s\' already defined in fixture factory',
            Fixture\FixtureFactory\Entity\Spaceship::class
        ));

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);
    }

    public function testDefineEntityThrowsExceptionWhenClassDoesNotExist(): void
    {
        $className = 'NotAClass';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Not a class: %s',
            $className
        ));

        $fixtureFactory->defineEntity($className);
    }

    public function testDefineEntityThrowsExceptionWhenClassNameDoesNotReferenceAnEntity(): void
    {
        $className = Fixture\FixtureFactory\NotAnEntity\User::class;

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Class "%s" is not a valid entity or mapped super class.',
            $className
        ));

        $fixtureFactory->defineEntity($className);
    }

    public function testDefineEntityThrowsExceptionWhenUsingFieldNameThatDoesNotExistInEntity(): void
    {
        $fieldName = 'pieType';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'No such field in %s: %s',
            Fixture\FixtureFactory\Entity\Spaceship::class,
            $fieldName
        ));

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            $fieldName => 'blueberry',
        ]);
    }

    public function testDefineEntityReturnsFixtureFactory(): void
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        self::assertSame($fixtureFactory, $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class));
    }

    public function testGetThrowsEntityDefinitionUnavailableWhenDefinitionIsUnavailable(): void
    {
        $entityManager = $this->prophesize(ORM\EntityManagerInterface::class)->reveal();

        $fixtureFactory = new FixtureFactory($entityManager);

        $this->expectException(Exception\EntityDefinitionUnavailable::class);
        $this->expectExceptionMessage('foo');

        $fixtureFactory->get('foo');
    }

    public function testThrowsWhenTryingToGiveNonexistentFieldsWhileConstructing(): void
    {
        $fieldName = 'pieType';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Alpha',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Field(s) not in %s: \'%s\'',
            Fixture\FixtureFactory\Entity\Spaceship::class,
            $fieldName
        ));

        $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, [
            $fieldName => 'blueberry',
        ]);
    }

    public function testThrowsWhenTryingToGetLessThanOneInstance(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can only get >= 1 instances');

        $fixtureFactory->getList(Fixture\FixtureFactory\Entity\Spaceship::class, [], 0);
    }

    public function testAcceptsConstantValuesInEntityDefinitions(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'My BattleCruiser',
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('My BattleCruiser', $spaceship->getName());
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

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipOne */
        $spaceshipOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        $name = 'Superstar';

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipTwo */
        $spaceshipTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('M/S Star', $spaceshipOne->getName());
        self::assertSame('M/S Superstar', $spaceshipTwo->getName());
    }

    public function testValuesCanBeOverriddenAtCreationTime(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'My BattleCruiser',
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'My CattleBruiser',
        ]);

        self::assertSame('My CattleBruiser', $spaceship->getName());
    }

    public function testPreservesDefaultValuesOfEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\SpaceStation::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\SpaceStation::class);

        self::assertSame('Babylon5', $spaceship->getName());
    }

    public function testDoesNotCallTheConstructorOfTheEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, []);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertFalse($spaceship->constructorWasCalled());
    }

    public function testInstantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Battlestar Galaxy',
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $spaceship->getCrew());
        self::assertEmpty($spaceship->getCrew());
    }

    public function testArrayElementsAreMappedToCollectionAsscociationFields(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Person $personOne */
        $personOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        /** @var Fixture\FixtureFactory\Entity\Person $personTwo */
        $personTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Battlestar Galaxy',
            'crew' => [
                $personOne,
                $personTwo,
            ],
        ]);

        self::assertInstanceOf(Common\Collections\ArrayCollection::class, $spaceship->getCrew());

        self::assertTrue($spaceship->getCrew()->contains($personOne));
        self::assertTrue($spaceship->getCrew()->contains($personTwo));
    }

    public function testUnspecifiedFieldsAreLeftNull(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertNull($spaceship->getName());
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

        /** @var Fixture\FixtureFactory\Entity\Person $person */
        $person = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        $spaceship = $person->getSpaceship();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Spaceship::class, $spaceship);

        self::assertContains($person, $spaceship->getCrew());
    }

    public function testUnidirectionalReferencesWorkAsUsual(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Badge::class, [
            'owner' => FieldDef::reference(Fixture\FixtureFactory\Entity\Person::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class);

        /** @var Fixture\FixtureFactory\Entity\Badge $badge */
        $badge = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Badge::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Person::class, $badge->getOwner());
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

        /** @var Fixture\FixtureFactory\Entity\Person $person */
        $person = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class, [
            'spaceship' => null,
        ]);

        self::assertNull($person->getSpaceship());
    }

    public function testAfterGettingAnEntityAsASingletonGettingTheEntityAgainReturnsTheSameObject(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame($spaceship, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
        self::assertSame($spaceship, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
    }

    public function testGetAsSingletonMethodAcceptsFieldOverridesLikeGet(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipOne */
        $spaceshipOne = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Beta',
        ]);

        self::assertSame('Beta', $spaceshipOne->getName());

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipTwo */
        $spaceshipTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('Beta', $spaceshipTwo->getName());
    }

    public function testThrowsAnErrorWhenCallingGetSingletonTwiceOnTheSameEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Alpha',
        ]);

        $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Already a singleton: %s',
            Fixture\FixtureFactory\Entity\Spaceship::class
        ));

        $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);
    }

    //TODO: should it be an error to get() a singleton with overrides?

    public function testAllowsSettingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $spaceship = new Fixture\FixtureFactory\Entity\Spaceship('The mothership');

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, $spaceship);

        self::assertSame($spaceship, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
    }

    public function testAllowsUnsettingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $spaceship = new Fixture\FixtureFactory\Entity\Spaceship('The mothership');

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, $spaceship);
        $fixtureFactory->unsetSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertNotSame($spaceship, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
    }

    public function testAllowsOverwritingExistingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $spaceshipOne = new Fixture\FixtureFactory\Entity\Spaceship('The mothership');
        $spaceshipTwo = new Fixture\FixtureFactory\Entity\Spaceship('The battlecruiser');

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, $spaceshipOne);
        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Spaceship::class, $spaceshipTwo);

        self::assertSame($spaceshipTwo, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class));
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

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        $crew = $spaceship->getCrew();

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

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Person $personOne */
        $personOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        /** @var Fixture\FixtureFactory\Entity\Person $personTwo */
        $personTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        self::assertContains($personOne, $spaceship->getCrew());
        self::assertContains($personTwo, $spaceship->getCrew());
    }

    public function testCanInvokeACallbackAfterObjectConstruction(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(
            Fixture\FixtureFactory\Entity\Spaceship::class,
            [
                'name' => 'Foo',
            ],
            [
                'afterCreate' => static function (Fixture\FixtureFactory\Entity\Spaceship $spaceship, array $fieldValues): void {
                    $spaceship->setName($spaceship->getName() . '-' . $fieldValues['name']);
                },
            ]
        );

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('Foo-Foo', $spaceship->getName());
    }

    public function testTheAfterCreateCallbackCanBeUsedToCallTheConstructor(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(
            Fixture\FixtureFactory\Entity\Spaceship::class,
            [
                'name' => 'Foo',
            ],
            [
                'afterCreate' => static function (Fixture\FixtureFactory\Entity\Spaceship $spaceship, array $fieldValues): void {
                    $spaceship->__construct($fieldValues['name'] . 'Master');
                },
            ]
        );

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceship */
        $spaceship = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Xoo',
        ]);

        self::assertTrue($spaceship->constructorWasCalled());
        self::assertSame('XooMaster', $spaceship->getName());
    }

    public function testReferencedObjectShouldBeCreatedAutomatically(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Person::class, [
            'name' => 'Eve',
            'spaceship' => FieldDef::reference(Fixture\FixtureFactory\Entity\Spaceship::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Person $personOne */
        $personOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        /** @var Fixture\FixtureFactory\Entity\Person $personTwo */
        $personTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Person::class);

        $spaceshipOne = $personOne->getSpaceship();
        $spaceshipTwo = $personTwo->getSpaceship();

        self::assertNotNull($spaceshipOne);
        self::assertNotNull($spaceshipTwo);
        self::assertNotSame($spaceshipOne, $spaceshipTwo);
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

        /** @var Fixture\FixtureFactory\Entity\Badge $badge */
        $badge = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Badge::class);

        $owner = $badge->getOwner();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Person::class, $owner);

        self::assertNotNull($owner->getSpaceship());
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

        /** @var Fixture\FixtureFactory\Entity\Badge $badgeOne */
        $badgeOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Badge::class);

        /** @var Fixture\FixtureFactory\Entity\Badge $badgeTwo */
        $badgeTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Badge::class);

        $ownerOne = $badgeOne->getOwner();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Person::class, $ownerOne);

        $ownerTwo = $badgeTwo->getOwner();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Person::class, $ownerTwo);

        self::assertNotSame($ownerOne, $ownerTwo);
        self::assertSame($ownerOne->getSpaceship(), $ownerTwo->getSpaceship());
    }

    public function testSequenceGeneratorCallsAFunctionWithAnIncrementingArgument(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => FieldDef::sequence(static function ($n) {
                return "Alpha {$n}";
            }),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipOne */
        $spaceshipOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipTwo */
        $spaceshipTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipThree */
        $spaceshipThree = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipFour */
        $spaceshipFour = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('Alpha 1', $spaceshipOne->getName());
        self::assertSame('Alpha 2', $spaceshipTwo->getName());
        self::assertSame('Alpha 3', $spaceshipThree->getName());
        self::assertSame('Alpha 4', $spaceshipFour->getName());
    }

    public function testSequenceGeneratorCanTakeAPlaceholderString(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => FieldDef::sequence('Beta %d'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipOne */
        $spaceshipOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipTwo */
        $spaceshipTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipThree */
        $spaceshipThree = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipFour */
        $spaceshipFour = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('Beta 1', $spaceshipOne->getName());
        self::assertSame('Beta 2', $spaceshipTwo->getName());
        self::assertSame('Beta 3', $spaceshipThree->getName());
        self::assertSame('Beta 4', $spaceshipFour->getName());
    }

    public function testSequenceGeneratorCanTakeAStringToAppendTo(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => FieldDef::sequence('Gamma '),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipOne */
        $spaceshipOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipTwo */
        $spaceshipTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipThree */
        $spaceshipThree = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $spaceshipFour */
        $spaceshipFour = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        self::assertSame('Gamma 1', $spaceshipOne->getName());
        self::assertSame('Gamma 2', $spaceshipTwo->getName());
        self::assertSame('Gamma 3', $spaceshipThree->getName());
        self::assertSame('Gamma 4', $spaceshipFour->getName());
    }
}
