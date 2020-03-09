<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;
use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class BidirectionalReferencesTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function bidirectionalOntToManyReferencesAreAssignedBothWays()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class)
        ]);

        $person = $fixtureFactory->get(Entity\Person::class);
        $ship = $person->getSpaceShip();

        $this->assertContains($person, $ship->getCrew());
    }

    /**
     * @test
     */
    public function unidirectionalReferencesWorkAsUsual()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\Badge::class, [
            'owner' => FieldDef::reference(Entity\Person::class)
        ]);
        $fixtureFactory->defineEntity(Entity\Person::class);

        $this->assertInstanceOf(Entity\Person::class, $fixtureFactory->get(Entity\Badge::class)->getOwner());
    }

    /**
     * @test
     */
    public function whenTheOneSideIsASingletonItMayGetSeveralChildObjects()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Entity\Person::class, [
            'spaceShip' => FieldDef::reference(Entity\SpaceShip::class)
        ]);

        $ship = $fixtureFactory->getAsSingleton(Entity\SpaceShip::class);
        $p1 = $fixtureFactory->get(Entity\Person::class);
        $p2 = $fixtureFactory->get(Entity\Person::class);

        $this->assertContains($p1, $ship->getCrew());
        $this->assertContains($p2, $ship->getCrew());
    }
}
