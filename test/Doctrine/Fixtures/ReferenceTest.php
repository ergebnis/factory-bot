<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

class ReferenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person::class, [
            'name' => 'Eve',
            'spaceShip' => FieldDef::reference(TestEntity\SpaceShip::class)
        ]);
    }

    /**
     * @test
     */
    public function referencedObjectShouldBeCreatedAutomatically()
    {
        $ss1 = $this->factory->get(TestEntity\Person::class)->getSpaceShip();
        $ss2 = $this->factory->get(TestEntity\Person::class)->getSpaceShip();

        $this->assertNotNull($ss1);
        $this->assertNotNull($ss2);
        $this->assertNotSame($ss1, $ss2);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeNullable()
    {
        $person = $this->factory->get(TestEntity\Person::class, ['spaceShip' => null]);

        $this->assertNull($person->getSpaceShip());
    }
}
