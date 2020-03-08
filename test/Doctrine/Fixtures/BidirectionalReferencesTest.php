<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FieldDef;

class BidirectionalReferencesTest extends TestCase
{
    /**
     * @test
     */
    public function bidirectionalOntToManyReferencesAreAssignedBothWays()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);
        $this->factory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class)
        ]);

        $person = $this->factory->get(Entity\Person::class);
        $ship = $person->getSpaceShip();

        $this->assertContains($person, $ship->getCrew());
    }

    /**
     * @test
     */
    public function unidirectionalReferencesWorkAsUsual()
    {
        $this->factory->defineEntity(Entity\Badge::class, [
            'owner' => FieldDef::reference(Entity\Person::class)
        ]);
        $this->factory->defineEntity(Entity\Person::class);

        $this->assertInstanceOf(Entity\Person::class, $this->factory->get(Entity\Badge::class)->getOwner());
    }

    /**
     * @test
     */
    public function whenTheOneSideIsASingletonItMayGetSeveralChildObjects()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);
        $this->factory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class)
        ]);

        $ship = $this->factory->getAsSingleton(Entity\SpaceShip::class);
        $p1 = $this->factory->get(Entity\Person::class);
        $p2 = $this->factory->get(Entity\Person::class);

        $this->assertContains($p1, $ship->getCrew());
        $this->assertContains($p2, $ship->getCrew());
    }
}
