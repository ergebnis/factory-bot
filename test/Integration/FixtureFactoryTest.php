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

use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Reference
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Closure
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testCreateOnePersistsEntityWhenPersistOnGetHasBeenTurnedOn(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        $fixtureFactory->persistOnGet();

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $entityManager->flush();

        self::assertNotNull($organization->id());
        self::assertSame($organization, $entityManager->find(Fixture\FixtureFactory\Entity\Organization::class, $organization->id()));
    }

    public function testCreateOneDoesNotNotPersistEntityByDefault(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $entityManager->flush();

        self::assertNull($organization->id());

        $query = $entityManager->createQueryBuilder()
            ->select('organization')
            ->from(Fixture\FixtureFactory\Entity\Organization::class, 'organization')
            ->getQuery();

        self::assertEmpty($query->getResult());
    }

    public function testCreateOneDoesNotPersistEmbeddablesWhenPersistOnGetHasBeenTurnedOn(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Avatar::class, [
            'url' => FieldDefinition::closure(static function () use ($faker): string {
                return $faker->imageUrl();
            }),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class, [
            'avatar' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Avatar::class),
            'login' => $faker->userName,
        ]);

        $fixtureFactory->persistOnGet();

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        $entityManager->flush();

        $this->addToAssertionCount(1);
    }
}
