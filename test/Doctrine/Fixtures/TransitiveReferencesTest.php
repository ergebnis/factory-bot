<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FieldDef;

class TransitiveReferencesTest extends TestCase
{
    private function simpleSetup()
    {
        $this->factory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class),
        ]);
        $this->factory->defineEntity(Entity\Badge::class, [
            'owner' => FieldDef::reference(Entity\Person::class)
        ]);
        $this->factory->defineEntity(Entity\SpaceShip::class);
    }

    /**
     * @test
     */
    public function referencesGetInstantiatedTransitively()
    {
        $this->simpleSetup();

        $badge = $this->factory->get(Entity\Badge::class);

        $this->assertNotNull($badge->getOwner()->getSpaceShip());
    }

    /**
     * @test
     */
    public function transitiveReferencesWorkWithSingletons()
    {
        $this->simpleSetup();

        $this->factory->getAsSingleton(Entity\SpaceShip::class);
        $badge1 = $this->factory->get(Entity\Badge::class);
        $badge2 = $this->factory->get(Entity\Badge::class);

        $this->assertNotSame($badge1->getOwner(), $badge2->getOwner());
        $this->assertSame($badge1->getOwner()->getSpaceShip(), $badge2->getOwner()->getSpaceShip());
    }
}
