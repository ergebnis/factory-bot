<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class ExtraConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function canInvokeACallbackAfterObjectConstruction()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'name' => 'Foo'
        ], [
            'afterCreate' => function (Entity\SpaceShip $ss, array $fieldValues) {
                $ss->setName($ss->getName() . '-' . $fieldValues['name']);
            }
        ]);
        $ss = $fixtureFactory->get(Entity\SpaceShip::class);

        $this->assertSame("Foo-Foo", $ss->getName());
    }

    /**
     * @test
     */
    public function theAfterCreateCallbackCanBeUsedToCallTheConstructor()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'name' => 'Foo'
        ], [
            'afterCreate' => function (Entity\SpaceShip $ss, array $fieldValues) {
                $ss->__construct($fieldValues['name'] . 'Master');
            }
        ]);
        $ss = $fixtureFactory->get(Entity\SpaceShip::class, ['name' => 'Xoo']);

        $this->assertTrue($ss->constructorWasCalled());
        $this->assertSame('XooMaster', $ss->getName());
    }
}
