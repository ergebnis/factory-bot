<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

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
            ->defineEntity(TestEntity\SpaceShip::class, [
                'name' => 'My BattleCruiser'
            ])
            ->get(TestEntity\SpaceShip::class);

        $this->assertSame('My BattleCruiser', $ss->getName());
    }

    /**
     * @test
     */
    public function acceptsGeneratorFunctionsInEntityDefinitions()
    {
        $name = "Star";
        $this->factory->defineEntity(TestEntity\SpaceShip::class, [
            'name' => function () use (&$name) {
                return "M/S $name";
            }
        ]);

        $this->assertSame('M/S Star', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $name = "Superstar";
        $this->assertSame('M/S Superstar', $this->factory->get(TestEntity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function valuesCanBeOverriddenAtCreationTime()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceShip::class, [
                'name' => 'My BattleCruiser'
            ])
            ->get(TestEntity\SpaceShip::class, ['name' => 'My CattleBruiser']);
        $this->assertSame('My CattleBruiser', $ss->getName());
    }

    /**
     * @test
     */
    public function preservesDefaultValuesOfEntity()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceStation::class)
            ->get(TestEntity\SpaceStation::class);
        $this->assertSame('Babylon5', $ss->getName());
    }

    /**
     * @test
     */
    public function doesNotCallTheConstructorOfTheEntity()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceShip::class, [])
            ->get(TestEntity\SpaceShip::class);
        $this->assertFalse($ss->constructorWasCalled());
    }

    /**
     * @test
     */
    public function instantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceShip::class, [
                'name' => 'Battlestar Galaxy'
            ])
            ->get(TestEntity\SpaceShip::class);

        $this->assertInstanceOf(ArrayCollection::class, $ss->getCrew());
        $this->assertEmpty($ss->getCrew());
    }

    /**
     * @test
     */
    public function arrayElementsAreMappedToCollectionAsscociationFields()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person::class, [
            'spaceShip' => FieldDef::reference(TestEntity\SpaceShip::class)
        ]);

        $p1 = $this->factory->get(TestEntity\Person::class);
        $p2 = $this->factory->get(TestEntity\Person::class);

        $ship = $this->factory->get(TestEntity\SpaceShip::class, [
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
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->assertNull($this->factory->get(TestEntity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function entityIsDefinedToDefaultNamespace()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person\User::class);

        $this->assertInstanceOf(
            TestEntity\SpaceShip::class,
            $this->factory->get(TestEntity\SpaceShip::class)
        );

        $this->assertInstanceOf(
            TestEntity\Person\User::class,
            $this->factory->get(TestEntity\Person\User::class)
        );
    }

    /**
     * @test
     */
    public function entityCanBeDefinedToAnotherNamespace()
    {
        $this->factory->defineEntity(
            TestAnotherEntity\Artist::class
        );

        $this->assertInstanceOf(
            TestAnotherEntity\Artist::class,
            $this->factory->get(
                TestAnotherEntity\Artist::class
            )
        );
    }

    /**
     * @test
     */
    public function returnsListOfEntities()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);

        $this->assertCount(1, $this->factory->getList(TestEntity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function canSpecifyNumberOfReturnedInstances()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);

        $this->assertCount(5, $this->factory->getList(TestEntity\SpaceShip::class, [], 5));
    }
}
