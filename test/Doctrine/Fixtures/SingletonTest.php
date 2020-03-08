<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

class SingletonTest extends TestCase
{
    /**
     * @test
     */
    public function afterGettingAnEntityAsASingletonGettingTheEntityAgainReturnsTheSameObject()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);

        $ss = $this->factory->getAsSingleton(TestEntity\SpaceShip::class);

        $this->assertSame($ss, $this->factory->get(TestEntity\SpaceShip::class));
        $this->assertSame($ss, $this->factory->get(TestEntity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function getAsSingletonMethodAcceptsFieldOverridesLikeGet()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);

        $ss = $this->factory->getAsSingleton(TestEntity\SpaceShip::class, ['name' => 'Beta']);
        $this->assertSame('Beta', $ss->getName());
        $this->assertSame('Beta', $this->factory->get(TestEntity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function throwsAnErrorWhenCallingGetSingletonTwiceOnTheSameEntity()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class, ['name' => 'Alpha']);
        $this->factory->getAsSingleton(TestEntity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $this->factory->getAsSingleton(TestEntity\SpaceShip::class);
    }

    //TODO: should it be an error to get() a singleton with overrides?

    /**
     * @test
     */
    public function allowsSettingSingletons()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $ss = new TestEntity\SpaceShip("The mothership");

        $this->factory->setSingleton(TestEntity\SpaceShip::class, $ss);

        $this->assertSame($ss, $this->factory->get(TestEntity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function allowsUnsettingSingletons()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $ss = new TestEntity\SpaceShip("The mothership");

        $this->factory->setSingleton(TestEntity\SpaceShip::class, $ss);
        $this->factory->unsetSingleton(TestEntity\SpaceShip::class);

        $this->assertNotSame($ss, $this->factory->get(TestEntity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function allowsOverwritingExistingSingletons()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $ss1 = new TestEntity\SpaceShip("The mothership");
        $ss2 = new TestEntity\SpaceShip("The battlecruiser");

        $this->factory->setSingleton(TestEntity\SpaceShip::class, $ss1);
        $this->factory->setSingleton(TestEntity\SpaceShip::class, $ss2);

        $this->assertSame($ss2, $this->factory->get(TestEntity\SpaceShip::class));
    }
}
