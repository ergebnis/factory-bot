<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity;
use Doctrine\Common\Collections\ArrayCollection;

class ReferencesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory->defineEntity(Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Entity\Person::class)
        ]);

        $this->factory->defineEntity(Entity\Person::class, [
            'name' => 'Eve',
        ]);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeCreatedAutomatically()
    {
        /** @var Entity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(Entity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf(ArrayCollection::class, $crew);
        $this->assertContainsOnly(Entity\Person::class, $crew);
        $this->assertCount(1, $crew);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeOverrideable()
    {
        $count = 5;

        /** @var Entity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(Entity\SpaceShip::class, [
            'crew' => $this->factory->getList(Entity\Person::class, [], $count),
        ]);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf(ArrayCollection::class, $crew);
        $this->assertContainsOnly(Entity\Person::class, $crew);
        $this->assertCount($count, $crew);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeNullable()
    {
        /** @var Entity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(Entity\SpaceShip::class, [
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
        /** @var Entity\Person $person*/
        $person = $this->factory->getAsSingleton(Entity\Person::class);

        /** @var Entity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(Entity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf(ArrayCollection::class, $crew);
        $this->assertContains($person, $crew);
        $this->assertCount(1, $crew);
    }
}
