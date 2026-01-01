<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Integration;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\EntityDefinition;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FieldResolution;
use Ergebnis\FactoryBot\FixtureFactory;
use Example\Entity;
use Faker\Generator;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FixtureFactory::class)]
#[Framework\Attributes\UsesClass(Count::class)]
#[Framework\Attributes\UsesClass(EntityDefinition::class)]
#[Framework\Attributes\UsesClass(FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Closure::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Reference::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Sequence::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FieldResolution\CountResolution\BetweenMinimumAndMaximumCount::class)]
#[Framework\Attributes\UsesClass(FieldResolution\FieldValueResolution\WithOrWithoutOptionalFieldValue::class)]
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testCreateOneDoesNotPersistEntityByDefault(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker,
        );

        $name = $faker->word();

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => $name,
        ]);

        $fixtureFactory->createOne(Entity\Organization::class);

        $entityManager->flush();
        $entityManager->clear();

        $organization = $entityManager->getRepository(Entity\Organization::class)->findOneBy([
            'name' => $name,
        ]);

        self::assertNull($organization);
    }

    public function testCreateOnePersistsEntityWhenFixtureFactoryIsPersisting(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker,
        );

        $name = $faker->word();

        $fixtureFactory->define(Entity\Organization::class, [
            'id' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->uuid();
            }),
            'name' => $name,
        ]);

        $persistingFixtureFactory = $fixtureFactory->persisting();

        $persistingFixtureFactory->createOne(Entity\Organization::class);

        $entityManager->flush();
        $entityManager->clear();

        $organization = $entityManager->getRepository(Entity\Organization::class)->findOneBy([
            'name' => $name,
        ]);

        self::assertInstanceOf(Entity\Organization::class, $organization);
    }

    public function testCreateOneDoesNotPersistEmbeddablesWhenFixtureFactoryIsPersisting(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker,
        );

        $fixtureFactory->define(Entity\Avatar::class, [
            'url' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->imageUrl();
            }),
        ]);

        $fixtureFactory->define(Entity\User::class, [
            'avatar' => FieldDefinition::reference(Entity\Avatar::class),
            'id' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->uuid();
            }),
            'login' => $faker->userName(),
        ]);

        $persistingFixtureFactory = $fixtureFactory->persisting();

        $persistingFixtureFactory->createOne(Entity\User::class);

        $entityManager->flush();

        $this->addToAssertionCount(1);
    }

    public function testCreateManyDoesNotPersistEntitiesByDefault(): void
    {
        $entityManager = self::entityManager();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => FieldDefinition::sequence('name-%d'),
        ]);

        $fixtureFactory->createMany(
            Entity\Organization::class,
            Count::exact(5),
        );

        $entityManager->flush();
        $entityManager->clear();

        $organizations = $entityManager->getRepository(Entity\Organization::class)->findAll();

        self::assertEmpty($organizations);
    }

    public function testCreateManyPersistsEntitiesWhenFixtureFactoryIsPersisting(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            $entityManager,
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'id' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->unique()->uuid();
            }),
            'name' => FieldDefinition::sequence('name-%d'),
        ]);

        $persistingFixtureFactory = $fixtureFactory->persisting();

        $value = $faker->numberBetween(1, 5);

        $persistingFixtureFactory->createMany(
            Entity\Organization::class,
            Count::exact($value),
        );

        $entityManager->flush();
        $entityManager->clear();

        $organizations = $entityManager->getRepository(Entity\Organization::class)->findAll();

        self::assertCount($value, $organizations);
    }
}
