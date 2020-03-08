<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;

class SingletonTest extends TestCase
{
    /**
     * @test
     */
    public function afterGettingAnEntityAsASingletonGettingTheEntityAgainReturnsTheSameObject()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);

        $ss = $this->factory->getAsSingleton(Entity\SpaceShip::class);

        $this->assertSame($ss, $this->factory->get(Entity\SpaceShip::class));
        $this->assertSame($ss, $this->factory->get(Entity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function getAsSingletonMethodAcceptsFieldOverridesLikeGet()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);

        $ss = $this->factory->getAsSingleton(Entity\SpaceShip::class, ['name' => 'Beta']);
        $this->assertSame('Beta', $ss->getName());
        $this->assertSame('Beta', $this->factory->get(Entity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function throwsAnErrorWhenCallingGetSingletonTwiceOnTheSameEntity()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class, ['name' => 'Alpha']);
        $this->factory->getAsSingleton(Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $this->factory->getAsSingleton(Entity\SpaceShip::class);
    }

    //TODO: should it be an error to get() a singleton with overrides?

    /**
     * @test
     */
    public function allowsSettingSingletons()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);
        $ss = new Entity\SpaceShip("The mothership");

        $this->factory->setSingleton(Entity\SpaceShip::class, $ss);

        $this->assertSame($ss, $this->factory->get(Entity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function allowsUnsettingSingletons()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);
        $ss = new Entity\SpaceShip("The mothership");

        $this->factory->setSingleton(Entity\SpaceShip::class, $ss);
        $this->factory->unsetSingleton(Entity\SpaceShip::class);

        $this->assertNotSame($ss, $this->factory->get(Entity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function allowsOverwritingExistingSingletons()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class);
        $ss1 = new Entity\SpaceShip("The mothership");
        $ss2 = new Entity\SpaceShip("The battlecruiser");

        $this->factory->setSingleton(Entity\SpaceShip::class, $ss1);
        $this->factory->setSingleton(Entity\SpaceShip::class, $ss2);

        $this->assertSame($ss2, $this->factory->get(Entity\SpaceShip::class));
    }
}
