<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity;
use Doctrine\Common\Collections\ArrayCollection;

class ReferencesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory->defineEntity(TestEntity\SpaceShip::class, [
            'crew' => FieldDef::references(TestEntity\Person::class)
        ]);

        $this->factory->defineEntity(TestEntity\Person::class, [
            'name' => 'Eve',
        ]);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeCreatedAutomatically()
    {
        /** @var TestEntity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(TestEntity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf(ArrayCollection::class, $crew);
        $this->assertContainsOnly(TestEntity\Person::class, $crew);
        $this->assertCount(1, $crew);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeOverrideable()
    {
        $count = 5;

        /** @var TestEntity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(TestEntity\SpaceShip::class, [
            'crew' => $this->factory->getList(TestEntity\Person::class, [], $count),
        ]);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf(ArrayCollection::class, $crew);
        $this->assertContainsOnly(TestEntity\Person::class, $crew);
        $this->assertCount($count, $crew);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeNullable()
    {
        /** @var TestEntity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(TestEntity\SpaceShip::class, [
            'crew' => null,
        ]);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf(ArrayCollection::class, $crew);
        $this->assertEmpty($crew);
    }

    /**
     * @test
     */
    public function referencedObjectsCanBeSingletons()
    {
        /** @var TestEntity\Person $person*/
        $person = $this->factory->getAsSingleton(TestEntity\Person::class);

        /** @var TestEntity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(TestEntity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf(ArrayCollection::class, $crew);
        $this->assertContains($person, $crew);
        $this->assertCount(1, $crew);
    }
}
