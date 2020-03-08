<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

class BidirectionalReferencesTest extends TestCase
{
    /**
     * @test
     */
    public function bidirectionalOntToManyReferencesAreAssignedBothWays()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person::class, [
            'spaceShip' => FieldDef::reference(TestEntity\SpaceShip::class)
        ]);

        $person = $this->factory->get(TestEntity\Person::class);
        $ship = $person->getSpaceShip();

        $this->assertContains($person, $ship->getCrew());
    }

    /**
     * @test
     */
    public function unidirectionalReferencesWorkAsUsual()
    {
        $this->factory->defineEntity(TestEntity\Badge::class, [
            'owner' => FieldDef::reference(TestEntity\Person::class)
        ]);
        $this->factory->defineEntity(TestEntity\Person::class);

        $this->assertInstanceOf(TestEntity\Person::class, $this->factory->get(TestEntity\Badge::class)->getOwner());
    }

    /**
     * @test
     */
    public function whenTheOneSideIsASingletonItMayGetSeveralChildObjects()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person::class, [
            'spaceShip' => FieldDef::reference(TestEntity\SpaceShip::class)
        ]);

        $ship = $this->factory->getAsSingleton(TestEntity\SpaceShip::class);
        $p1 = $this->factory->get(TestEntity\Person::class);
        $p2 = $this->factory->get(TestEntity\Person::class);

        $this->assertContains($p1, $ship->getCrew());
        $this->assertContains($p2, $ship->getCrew());
    }
}
