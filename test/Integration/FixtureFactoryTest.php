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

namespace Ergebnis\FactoryBot\Test\Integration;

use Doctrine\ORM\Mapping;
use Ergebnis\FactoryBot\FieldDef;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testAutomaticPersistCanBeTurnedOn(): void
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, ['name' => 'Zeta']);

        $fixtureFactory->persistOnGet();

        $ss = $fixtureFactory->get(Fixture\Entity\SpaceShip::class);
        $entityManager->flush();

        self::assertNotNull($ss->getId());
        self::assertSame($ss, $entityManager->find(Fixture\Entity\SpaceShip::class, $ss->getId()));
    }

    public function testDoesNotPersistByDefault(): void
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Fixture\Entity\SpaceShip::class, ['name' => 'Zeta']);

        $ss = $fixtureFactory->get(Fixture\Entity\SpaceShip::class);

        $entityManager->flush();

        self::assertNull($ss->getId());
        $q = $entityManager
            ->createQueryBuilder()
            ->select('ss')
            ->from(Fixture\Entity\SpaceShip::class, 'ss')
            ->getQuery();
        self::assertEmpty($q->getResult());
    }

    public function testDoesNotPersistEmbeddableWhenAutomaticPersistingIsTurnedOn(): void
    {
        $mappingClasses = [
            Mapping\Embeddable::class,
            Mapping\Embedded::class,
        ];

        foreach ($mappingClasses as $mappingClass) {
            if (!\class_exists($mappingClass)) {
                self::markTestSkipped('Doctrine Embeddable feature not available');
            }
        }

        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Fixture\Entity\Name::class, [
            'first' => FieldDef::sequence(static function () {
                $values = [
                    null,
                    'Doe',
                    'Smith',
                ];

                return $values[\array_rand($values)];
            }),
            'last' => FieldDef::sequence(static function () {
                $values = [
                    null,
                    'Jane',
                    'John',
                ];

                return $values[\array_rand($values)];
            }),
        ]);

        $fixtureFactory->defineEntity(Fixture\Entity\Commander::class, [
            'name' => FieldDef::reference(Fixture\Entity\Name::class),
        ]);

        $fixtureFactory->persistOnGet();

        $commander = $fixtureFactory->get(Fixture\Entity\Commander::class);

        self::assertInstanceOf(Fixture\Entity\Commander::class, $commander);
        self::assertInstanceOf(Fixture\Entity\Name::class, $commander->name());

        $entityManager->flush();
    }
}
