<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit;

use Ergebnis\FactoryBot\FieldDef;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDef
 * @uses \Ergebnis\FactoryBot\FieldDef
 */
final class SequenceTest extends AbstractTestCase
{
    public function testSequenceGeneratorCallsAFunctionWithAnIncrementingArgument(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'name' => FieldDef::sequence(static function ($n) {
                return "Alpha {$n}";
            }),
        ]);
        self::assertSame('Alpha 1', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Alpha 2', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Alpha 3', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Alpha 4', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
    }

    public function testSequenceGeneratorCanTakeAPlaceholderString(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'name' => FieldDef::sequence('Beta %d'),
        ]);
        self::assertSame('Beta 1', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Beta 2', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Beta 3', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Beta 4', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
    }

    public function testSequenceGeneratorCanTakeAStringToAppendTo(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'name' => FieldDef::sequence('Gamma '),
        ]);
        self::assertSame('Gamma 1', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Gamma 2', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Gamma 3', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
        self::assertSame('Gamma 4', $fixtureFactory->get(Fixture\Entity\SpaceShip::class)->getName());
    }
}
