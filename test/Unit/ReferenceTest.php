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
final class ReferenceTest extends AbstractTestCase
{
    public function testReferencedObjectShouldBeCreatedAutomatically(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'name' => 'Eve',
            'spaceShip' => FieldDef::reference(Fixture\Entity\SpaceShip::class),
        ]);

        $ss1 = $fixtureFactory->get(Fixture\Entity\Person::class)->getSpaceShip();
        $ss2 = $fixtureFactory->get(Fixture\Entity\Person::class)->getSpaceShip();

        self::assertNotNull($ss1);
        self::assertNotNull($ss2);
        self::assertNotSame($ss1, $ss2);
    }

    public function testReferencedObjectsShouldBeNullable(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
        $fixtureFactory->defineEntity(Fixture\Entity\Person::class, [
            'name' => 'Eve',
            'spaceShip' => FieldDef::reference(Fixture\Entity\SpaceShip::class),
        ]);

        $person = $fixtureFactory->get(Fixture\Entity\Person::class, ['spaceShip' => null]);

        self::assertNull($person->getSpaceShip());
    }
}
