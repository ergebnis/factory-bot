<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class SingletonTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function afterGettingAnEntityAsASingletonGettingTheEntityAgainReturnsTheSameObject()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $ss = $fixtureFactory->getAsSingleton(Entity\SpaceShip::class);

        $this->assertSame($ss, $fixtureFactory->get(Entity\SpaceShip::class));
        $this->assertSame($ss, $fixtureFactory->get(Entity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function getAsSingletonMethodAcceptsFieldOverridesLikeGet()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $ss = $fixtureFactory->getAsSingleton(Entity\SpaceShip::class, ['name' => 'Beta']);
        $this->assertSame('Beta', $ss->getName());
        $this->assertSame('Beta', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function throwsAnErrorWhenCallingGetSingletonTwiceOnTheSameEntity()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, ['name' => 'Alpha']);
        $fixtureFactory->getAsSingleton(Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->getAsSingleton(Entity\SpaceShip::class);
    }

    //TODO: should it be an error to get() a singleton with overrides?

    /**
     * @test
     */
    public function allowsSettingSingletons()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $ss = new Entity\SpaceShip("The mothership");

        $fixtureFactory->setSingleton(Entity\SpaceShip::class, $ss);

        $this->assertSame($ss, $fixtureFactory->get(Entity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function allowsUnsettingSingletons()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $ss = new Entity\SpaceShip("The mothership");

        $fixtureFactory->setSingleton(Entity\SpaceShip::class, $ss);
        $fixtureFactory->unsetSingleton(Entity\SpaceShip::class);

        $this->assertNotSame($ss, $fixtureFactory->get(Entity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function allowsOverwritingExistingSingletons()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
        $ss1 = new Entity\SpaceShip("The mothership");
        $ss2 = new Entity\SpaceShip("The battlecruiser");

        $fixtureFactory->setSingleton(Entity\SpaceShip::class, $ss1);
        $fixtureFactory->setSingleton(Entity\SpaceShip::class, $ss2);

        $this->assertSame($ss2, $fixtureFactory->get(Entity\SpaceShip::class));
    }
}
