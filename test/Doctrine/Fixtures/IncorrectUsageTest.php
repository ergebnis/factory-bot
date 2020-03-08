<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;

class IncorrectUsageTest extends TestCase
{
    /**
     * @test
     */
    public function throwsWhenTryingToDefineTheSameEntityTwice()
    {
        $factory = $this->factory->defineEntity(Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $factory->defineEntity(Entity\SpaceShip::class);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEvenClasses()
    {
        $this->expectException(\Exception::class);

        $this->factory->defineEntity('NotAClass');
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEntities()
    {
        $this->assertTrue(class_exists(Entity\NotAnEntity::class, true));

        $this->expectException(\Exception::class);

        $this->factory->defineEntity(Entity\NotAnEntity::class);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineNonexistentFields()
    {
        $this->expectException(\Exception::class);

        $this->factory->defineEntity(Entity\SpaceShip::class, [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGiveNonexistentFieldsWhileConstructing()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class, ['name' => 'Alpha']);

        $this->expectException(\Exception::class);

        $this->factory->get(Entity\SpaceShip::class, [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGetLessThanOneInstance()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $this->factory->getList(Entity\SpaceShip::class, [], 0);
    }
}
