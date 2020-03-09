<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Provider\Doctrine\FixtureFactory;
use FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity;
use Doctrine\Common\Collections\ArrayCollection;

class ReferencesTest extends TestCase
{
    /**
     * @test
     */
    public function referencedObjectsShouldBeCreatedAutomatically()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Entity\Person::class)
        ]);

        $fixtureFactory->defineEntity(Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Entity\SpaceShip $spaceShip */
        $spaceShip = $fixtureFactory->get(Entity\SpaceShip::class);

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

        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Entity\Person::class)
        ]);

        $fixtureFactory->defineEntity(Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Entity\SpaceShip $spaceShip */
        $spaceShip = $fixtureFactory->get(Entity\SpaceShip::class, [
            'crew' => $fixtureFactory->getList(Entity\Person::class, [], $count),
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
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Entity\Person::class)
        ]);

        $fixtureFactory->defineEntity(Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Entity\SpaceShip $spaceShip */
        $spaceShip = $fixtureFactory->get(Entity\SpaceShip::class, [
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
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'crew' => FieldDef::references(Entity\Person::class)
        ]);

        $fixtureFactory->defineEntity(Entity\Person::class, [
            'name' => 'Eve',
        ]);

        /** @var Entity\Person $person*/
        $person = $fixtureFactory->getAsSingleton(Entity\Person::class);

        /** @var Entity\SpaceShip $spaceShip */
        $spaceShip = $fixtureFactory->get(Entity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf(ArrayCollection::class, $crew);
        $this->assertContains($person, $crew);
        $this->assertCount(1, $crew);
    }
}
