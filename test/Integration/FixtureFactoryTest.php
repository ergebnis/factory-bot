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

use Ergebnis\FactoryBot\FieldDef;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;
use Ergebnis\Test\Util\Helper;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    use Helper;

    public function testAutomaticPersistCanBeTurnedOn(): void
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => self::faker()->word,
        ]);

        $fixtureFactory->persistOnGet();

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        $entityManager->flush();

        self::assertNotNull($organization->id());
        self::assertSame($organization, $entityManager->find(Fixture\FixtureFactory\Entity\Organization::class, $organization->id()));
    }

    public function testDoesNotPersistByDefault(): void
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        $entityManager->flush();

        self::assertNull($organization->id());

        $query = $entityManager->createQueryBuilder()
            ->select('organization')
            ->from(Fixture\FixtureFactory\Entity\Organization::class, 'organization')
            ->getQuery();

        self::assertEmpty($query->getResult());
    }

    public function testDoesNotPersistEmbeddableWhenAutomaticPersistingIsTurnedOn(): void
    {
        $faker = self::faker();

        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Avatar::class, [
            'url' => FieldDef::sequence(static function () use ($faker): string {
                return $faker->imageUrl();
            }),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class, [
            'avatar' => FieldDef::reference(Fixture\FixtureFactory\Entity\Avatar::class),
            'login' => $faker->userName,
        ]);

        $fixtureFactory->persistOnGet();

        $fixtureFactory->get(Fixture\FixtureFactory\Entity\User::class);

        $entityManager->flush();

        $this->addToAssertionCount(1);
    }
}
