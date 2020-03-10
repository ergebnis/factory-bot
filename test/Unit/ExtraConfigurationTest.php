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
 * @uses \Ergebnis\FactoryBot\FieldDef
 */
final class ExtraConfigurationTest extends AbstractTestCase
{
    public function testCanInvokeACallbackAfterObjectConstruction(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'name' => 'Foo',
        ], [
            'afterCreate' => static function (Fixture\Entity\SpaceShip $ss, array $fieldValues): void {
                $ss->setName($ss->getName() . '-' . $fieldValues['name']);
            },
        ]);
        $ss = $fixtureFactory->get(Fixture\Entity\SpaceShip::class);

        self::assertSame('Foo-Foo', $ss->getName());
    }

    public function testTheAfterCreateCallbackCanBeUsedToCallTheConstructor(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, [
            'name' => 'Foo',
        ], [
            'afterCreate' => static function (Fixture\Entity\SpaceShip $ss, array $fieldValues): void {
                $ss->__construct($fieldValues['name'] . 'Master');
            },
        ]);
        $ss = $fixtureFactory->get(Fixture\Entity\SpaceShip::class, ['name' => 'Xoo']);

        self::assertTrue($ss->constructorWasCalled());
        self::assertSame('XooMaster', $ss->getName());
    }
}
