<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class ReferenceTest extends TestCase
{
    /**
     * @test
     */
    public function referencedObjectShouldBeCreatedAutomatically()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Entity\Person::class, [
            'name' => 'Eve',
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class)
        ]);

        $ss1 = $fixtureFactory->get(Entity\Person::class)->getSpaceShip();
        $ss2 = $fixtureFactory->get(Entity\Person::class)->getSpaceShip();

        $this->assertNotNull($ss1);
        $this->assertNotNull($ss2);
        $this->assertNotSame($ss1, $ss2);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeNullable()
    {
        $fixtureFactory = new FixtureFactory($this->em);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Entity\Person::class, [
            'name' => 'Eve',
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class)
        ]);

        $person = $fixtureFactory->get(Entity\Person::class, ['spaceShip' => null]);

        $this->assertNull($person->getSpaceShip());
    }
}
