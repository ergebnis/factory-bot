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

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\Count
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Closure
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Reference
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Sequence
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testCreateOneDoesNotPersistEntityByDefault(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker
        );

        $name = $faker->word;

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $entityManager->flush();
        $entityManager->clear();

        $organization = $entityManager->getRepository(Fixture\FixtureFactory\Entity\Organization::class)->findOneBy([
            'name' => $name,
        ]);

        self::assertNull($organization);
    }

    public function testCreateOnePersistsEntityWhenPersistOnGetHasBeenEnabled(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker
        );

        $name = $faker->word;

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        $fixtureFactory->persistOnGet();

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $entityManager->flush();
        $entityManager->clear();

        $organization = $entityManager->getRepository(Fixture\FixtureFactory\Entity\Organization::class)->findOneBy([
            'name' => $name,
        ]);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Organization::class, $organization);
    }

    public function testCreateOneDoesNotPersistEntityWhenPersistOnGetHasBeenDisabled(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker
        );

        $name = $faker->word;

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        $fixtureFactory->persistOnGet();
        $fixtureFactory->persistOnGet(false);

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $entityManager->flush();
        $entityManager->clear();

        $organization = $entityManager->getRepository(Fixture\FixtureFactory\Entity\Organization::class)->findOneBy([
            'name' => $name,
        ]);

        self::assertNull($organization);
    }

    public function testCreateOneDoesNotPersistEmbeddablesWhenPersistOnGetHasBeenEnabled(): void
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

    public function testCreateManyDoesNotPersistEntitiesByDefault(): void
    {
        $entityManager = self::entityManager();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => FieldDefinition::sequence('name-%d'),
        ]);

        $fixtureFactory->createMany(
            Fixture\FixtureFactory\Entity\Organization::class,
            Count::exact(5)
        );

        $entityManager->flush();
        $entityManager->clear();

        $organizations = $entityManager->getRepository(Fixture\FixtureFactory\Entity\Organization::class)->findAll();

        self::assertEmpty($organizations);
    }

    public function testCreateManyPersistsEntitiesWhenPersistOnGetHasBeenEnabled(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => FieldDefinition::sequence('name-%d'),
        ]);

        $fixtureFactory->persistOnGet();

        $value = $faker->numberBetween(1, 5);

        $fixtureFactory->createMany(
            Fixture\FixtureFactory\Entity\Organization::class,
            Count::exact($value)
        );

        $entityManager->flush();
        $entityManager->clear();

        $organizations = $entityManager->getRepository(Fixture\FixtureFactory\Entity\Organization::class)->findAll();

        self::assertCount($value, $organizations);
    }

    public function testCreateManyDoesNotPersistEntitiesWhenPersistOnGetHasBeenDisabled(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => FieldDefinition::sequence('name-%d'),
        ]);

        $fixtureFactory->persistOnGet();
        $fixtureFactory->persistOnGet(false);

        $value = $faker->numberBetween(1, 5);

        $fixtureFactory->createMany(
            Fixture\FixtureFactory\Entity\Organization::class,
            Count::exact($value)
        );

        $entityManager->flush();
        $entityManager->clear();

        $organizations = $entityManager->getRepository(Fixture\FixtureFactory\Entity\Organization::class)->findAll();

        self::assertEmpty($organizations);
    }
}
