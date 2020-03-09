<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;
use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class BasicUsageTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function acceptsConstantValuesInEntityDefinitions()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Entity\SpaceShip::class, [
                'name' => 'My BattleCruiser'
            ])
            ->get(Entity\SpaceShip::class);

        $this->assertSame('My BattleCruiser', $ss->getName());
    }

    /**
     * @test
     */
    public function acceptsGeneratorFunctionsInEntityDefinitions()
    {
        $name = "Star";

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'name' => function () use (&$name) {
                return "M/S $name";
            }
        ]);

        $this->assertSame('M/S Star', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $name = "Superstar";
        $this->assertSame('M/S Superstar', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function valuesCanBeOverriddenAtCreationTime()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Entity\SpaceShip::class, [
                'name' => 'My BattleCruiser'
            ])
            ->get(Entity\SpaceShip::class, ['name' => 'My CattleBruiser']);
        $this->assertSame('My CattleBruiser', $ss->getName());
    }

    /**
     * @test
     */
    public function preservesDefaultValuesOfEntity()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Entity\SpaceStation::class)
            ->get(Entity\SpaceStation::class);
        $this->assertSame('Babylon5', $ss->getName());
    }

    /**
     * @test
     */
    public function doesNotCallTheConstructorOfTheEntity()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Entity\SpaceShip::class, [])
            ->get(Entity\SpaceShip::class);
        $this->assertFalse($ss->constructorWasCalled());
    }

    /**
     * @test
     */
    public function instantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $ss = $fixtureFactory
            ->defineEntity(Entity\SpaceShip::class, [
                'name' => 'Battlestar Galaxy'
            ])
            ->get(Entity\SpaceShip::class);

        $this->assertInstanceOf(ArrayCollection::class, $ss->getCrew());
        $this->assertEmpty($ss->getCrew());
    }

    /**
     * @test
     */
    public function arrayElementsAreMappedToCollectionAsscociationFields()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class)
        ]);

        $p1 = $fixtureFactory->get(Entity\Person::class);
        $p2 = $fixtureFactory->get(Entity\Person::class);

        $ship = $fixtureFactory->get(Entity\SpaceShip::class, [
            'name' => 'Battlestar Galaxy',
            'crew' => [$p1, $p2]
        ]);

        $this->assertInstanceOf(ArrayCollection::class, $ship->getCrew());
        $this->assertTrue($ship->getCrew()->contains($p1));
        $this->assertTrue($ship->getCrew()->contains($p2));
    }

    /**
     * @test
     */
    public function unspecifiedFieldsAreLeftNull()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $this->assertNull($fixtureFactory->get(Entity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function entityIsDefinedToDefaultNamespace()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Entity\Person\User::class);

        $this->assertInstanceOf(
            Entity\SpaceShip::class,
            $fixtureFactory->get(Entity\SpaceShip::class)
        );

        $this->assertInstanceOf(
            Entity\Person\User::class,
            $fixtureFactory->get(Entity\Person\User::class)
        );
    }

    /**
     * @test
     */
    public function entityCanBeDefinedToAnotherNamespace()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(
            Entity\Artist::class
        );

        $this->assertInstanceOf(
            Entity\Artist::class,
            $fixtureFactory->get(
                Entity\Artist::class
            )
        );
    }

    /**
     * @test
     */
    public function returnsListOfEntities()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $this->assertCount(1, $fixtureFactory->getList(Entity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function canSpecifyNumberOfReturnedInstances()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $this->assertCount(5, $fixtureFactory->getList(Entity\SpaceShip::class, [], 5));
    }
}
