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

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Zeta',
        ]);

        $fixtureFactory->persistOnGet();

        /** @var Fixture\FixtureFactory\Entity\Spaceship $ss */
        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        $entityManager->flush();

        self::assertNotNull($ss->getId());
        self::assertSame($ss, $entityManager->find(Fixture\FixtureFactory\Entity\Spaceship::class, $ss->getId()));
    }

    public function testDoesNotPersistByDefault(): void
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Spaceship::class, [
            'name' => 'Zeta',
        ]);

        /** @var Fixture\FixtureFactory\Entity\Spaceship $ss */
        $ss = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Spaceship::class);

        $entityManager->flush();

        self::assertNull($ss->getId());

        $q = $entityManager->createQueryBuilder()
            ->select('ss')
            ->from(Fixture\FixtureFactory\Entity\Spaceship::class, 'ss')
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

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Name::class, [
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

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Commander::class, [
            'name' => FieldDef::reference(Fixture\FixtureFactory\Entity\Name::class),
        ]);

        $fixtureFactory->persistOnGet();

        /** @var Fixture\FixtureFactory\Entity\Commander $commander */
        $commander = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Commander::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Name::class, $commander->name());

        $entityManager->flush();

        $this->addToAssertionCount(1);
    }
}
