<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

class IncorrectUsageTest extends TestCase
{
    /**
     * @test
     */
    public function throwsWhenTryingToDefineTheSameEntityTwice()
    {
        $factory = $this->factory->defineEntity(TestEntity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $factory->defineEntity(TestEntity\SpaceShip::class);
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
        $this->assertTrue(class_exists('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\NotAnEntity', true));

        $this->expectException(\Exception::class);

        $this->factory->defineEntity(TestEntity\NotAnEntity::class);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineNonexistentFields()
    {
        $this->expectException(\Exception::class);

        $this->factory->defineEntity(TestEntity\SpaceShip::class, [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGiveNonexistentFieldsWhileConstructing()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class, ['name' => 'Alpha']);

        $this->expectException(\Exception::class);

        $this->factory->get(TestEntity\SpaceShip::class, [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGetLessThanOneInstance()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $this->factory->getList(TestEntity\SpaceShip::class, [], 0);
    }
}
