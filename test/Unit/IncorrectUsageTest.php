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

use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDef
 */
final class IncorrectUsageTest extends AbstractTestCase
{
    public function testThrowsWhenTryingToDefineTheSameEntityTwice(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);
    }

    public function testThrowsWhenTryingToDefineEntitiesThatAreNotEvenClasses(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity('NotAClass');
    }

    public function testThrowsWhenTryingToDefineEntitiesThatAreNotEntities(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        self::assertTrue(\class_exists(Fixture\Entity\NotAnEntity::class, true));

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Fixture\Entity\NotAnEntity::class);
    }

    public function testThrowsWhenTryingToDefineNonexistentFields(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'pieType' => 'blueberry',
        ]);
    }

    public function testThrowsWhenTryingToGiveNonexistentFieldsWhileConstructing(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, ['name' => 'Alpha']);

        $this->expectException(\Exception::class);

        $fixtureFactory->get(Fixture\Entity\SpaceShip::class, [
            'pieType' => 'blueberry',
        ]);
    }

    public function testThrowsWhenTryingToGetLessThanOneInstance(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->getList(Fixture\Entity\SpaceShip::class, [], 0);
    }
}
