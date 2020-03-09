<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class IncorrectUsageTest extends TestCase
{
    /**
     * @test
     */
    public function throwsWhenTryingToDefineTheSameEntityTwice()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEvenClasses()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity('NotAClass');
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEntities()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $this->assertTrue(class_exists(Entity\NotAnEntity::class, true));

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Entity\NotAnEntity::class);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineNonexistentFields()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGiveNonexistentFieldsWhileConstructing()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, ['name' => 'Alpha']);

        $this->expectException(\Exception::class);

        $fixtureFactory->get(Entity\SpaceShip::class, [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGetLessThanOneInstance()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->getList(Entity\SpaceShip::class, [], 0);
    }
}
