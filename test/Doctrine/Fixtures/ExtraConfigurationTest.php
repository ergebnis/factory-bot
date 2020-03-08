<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

class ExtraConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function canInvokeACallbackAfterObjectConstruction()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class, [
            'name' => 'Foo'
        ], [
            'afterCreate' => function (TestEntity\SpaceShip $ss, array $fieldValues) {
                $ss->setName($ss->getName() . '-' . $fieldValues['name']);
            }
        ]);
        $ss = $this->factory->get(TestEntity\SpaceShip::class);

        $this->assertSame("Foo-Foo", $ss->getName());
    }

    /**
     * @test
     */
    public function theAfterCreateCallbackCanBeUsedToCallTheConstructor()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class, [
            'name' => 'Foo'
        ], [
            'afterCreate' => function (TestEntity\SpaceShip $ss, array $fieldValues) {
                $ss->__construct($fieldValues['name'] . 'Master');
            }
        ]);
        $ss = $this->factory->get(TestEntity\SpaceShip::class, ['name' => 'Xoo']);

        $this->assertTrue($ss->constructorWasCalled());
        $this->assertSame('XooMaster', $ss->getName());
    }
}
