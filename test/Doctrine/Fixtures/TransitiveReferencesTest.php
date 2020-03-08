<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

class TransitiveReferencesTest extends TestCase
{
    private function simpleSetup()
    {
        $this->factory->defineEntity(TestEntity\Person::class, [
            'spaceShip' => FieldDef::reference(TestEntity\SpaceShip::class),
        ]);
        $this->factory->defineEntity(TestEntity\Badge::class, [
            'owner' => FieldDef::reference(TestEntity\Person::class)
        ]);
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
    }

    /**
     * @test
     */
    public function referencesGetInstantiatedTransitively()
    {
        $this->simpleSetup();

        $badge = $this->factory->get(TestEntity\Badge::class);

        $this->assertNotNull($badge->getOwner()->getSpaceShip());
    }

    /**
     * @test
     */
    public function transitiveReferencesWorkWithSingletons()
    {
        $this->simpleSetup();

        $this->factory->getAsSingleton(TestEntity\SpaceShip::class);
        $badge1 = $this->factory->get(TestEntity\Badge::class);
        $badge2 = $this->factory->get(TestEntity\Badge::class);

        $this->assertNotSame($badge1->getOwner(), $badge2->getOwner());
        $this->assertSame($badge1->getOwner()->getSpaceShip(), $badge2->getOwner()->getSpaceShip());
    }
}
