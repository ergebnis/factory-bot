<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class TransitiveReferencesTest extends TestCase
{
    /**
     * @test
     */
    public function referencesGetInstantiatedTransitively()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class),
        ]);

        $fixtureFactory->defineEntity(Entity\Badge::class, [
            'owner' => FieldDef::reference(Entity\Person::class)
        ]);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $badge = $fixtureFactory->get(Entity\Badge::class);

        $this->assertNotNull($badge->getOwner()->getSpaceShip());
    }

    /**
     * @test
     */
    public function transitiveReferencesWorkWithSingletons()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class),
        ]);

        $fixtureFactory->defineEntity(Entity\Badge::class, [
            'owner' => FieldDef::reference(Entity\Person::class)
        ]);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $fixtureFactory->getAsSingleton(Entity\SpaceShip::class);
        $badge1 = $fixtureFactory->get(Entity\Badge::class);
        $badge2 = $fixtureFactory->get(Entity\Badge::class);

        $this->assertNotSame($badge1->getOwner(), $badge2->getOwner());
        $this->assertSame($badge1->getOwner()->getSpaceShip(), $badge2->getOwner()->getSpaceShip());
    }
}
