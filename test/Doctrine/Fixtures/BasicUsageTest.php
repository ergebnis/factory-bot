<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use FactoryGirl\Provider\Doctrine\FieldDef;

class BasicUsageTest extends TestCase
{
    /**
     * @test
     */
    public function acceptsConstantValuesInEntityDefinitions()
    {
        $ss = $this->factory
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
        $this->factory->defineEntity(Entity\SpaceShip::class, [
            'name' => function () use (&$name) {
                return "M/S $name";
            }
        ]);

        $this->assertSame('M/S Star', $this->factory->get(Entity\SpaceShip::class)->getName());
        $name = "Superstar";
        $this->assertSame('M/S Superstar', $this->factory->get(Entity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function valuesCanBeOverriddenAtCreationTime()
    {
        $ss = $this->factory
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
        $ss = $this->factory
            ->defineEntity(Entity\SpaceStation::class)
            ->get(Entity\SpaceStation::class);
        $this->assertSame('Babylon5', $ss->getName());
    }

    /**
     * @test
     */
    public function doesNotCallTheConstructorOfTheEntity()
    {
        $ss = $this->factory
            ->defineEntity(Entity\SpaceShip::class, [])
            ->get(Entity\SpaceShip::class);
        $this->assertFalse($ss->constructorWasCalled());
    }

    /**
     * @test
     */
    public function instantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified()
    {
        $ss = $this->factory
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
        $this->factory->defineEntity(Entity\SpaceShip::class);
        $this->factory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class)
        ]);

        $p1 = $this->factory->get(Entity\Person::class);
        $p2 = $this->factory->get(Entity\Person::class);

        $ship = $this->factory->get(Entity\SpaceShip::class, [
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
        $this->factory->defineEntity(Entity\SpaceShip::class);
        $this->assertNull($this->factory->get(Entity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function entityIsDefinedToDefaultNamespace()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);
        $this->factory->defineEntity(Entity\Person\User::class);

        $this->assertInstanceOf(
            Entity\SpaceShip::class,
            $this->factory->get(Entity\SpaceShip::class)
        );

        $this->assertInstanceOf(
            Entity\Person\User::class,
            $this->factory->get(Entity\Person\User::class)
        );
    }

    /**
     * @test
     */
    public function entityCanBeDefinedToAnotherNamespace()
    {
        $this->factory->defineEntity(
            Entity\Artist::class
        );

        $this->assertInstanceOf(
            Entity\Artist::class,
            $this->factory->get(
                Entity\Artist::class
            )
        );
    }

    /**
     * @test
     */
    public function returnsListOfEntities()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);

        $this->assertCount(1, $this->factory->getList(Entity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function canSpecifyNumberOfReturnedInstances()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);

        $this->assertCount(5, $this->factory->getList(Entity\SpaceShip::class, [], 5));
    }
}
