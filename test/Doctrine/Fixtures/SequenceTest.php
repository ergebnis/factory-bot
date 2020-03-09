<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;
use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class SequenceTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function sequenceGeneratorCallsAFunctionWithAnIncrementingArgument()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'name' => FieldDef::sequence(function ($n) {
                return "Alpha $n";
            })
        ]);
        $this->assertSame('Alpha 1', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Alpha 2', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Alpha 3', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Alpha 4', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function sequenceGeneratorCanTakeAPlaceholderString()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'name' => FieldDef::sequence("Beta %d")
        ]);
        $this->assertSame('Beta 1', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Beta 2', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Beta 3', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Beta 4', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function sequenceGeneratorCanTakeAStringToAppendTo()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'name' => FieldDef::sequence("Gamma ")
        ]);
        $this->assertSame('Gamma 1', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Gamma 2', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Gamma 3', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
        $this->assertSame('Gamma 4', $fixtureFactory->get(Entity\SpaceShip::class)->getName());
    }
}
