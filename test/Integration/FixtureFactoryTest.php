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

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\FixtureFactory::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Count::class)]
#[Framework\Attributes\UsesClass(FactoryBot\EntityDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Closure::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Reference::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Sequence::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\CountResolution\BetweenMinimumAndMaximumCount::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\FieldValueResolution\WithOrWithoutOptionalFieldValue::class)]
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testCreateOneDoesNotPersistEntityByDefault(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
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

        $fixtureFactory = new FactoryBot\FixtureFactory(
            $entityManager,
            $faker,
        );

        $name = $faker->word();

        $fixtureFactory->define(Entity\Organization::class, [
            'id' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
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

    #[Framework\Attributes\DoesNotPerformAssertions()]
    public function testCreateOneDoesNotPersistEmbeddablesWhenFixtureFactoryIsPersisting(): void
    {
        $entityManager = self::entityManager();
        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            $entityManager,
            $faker,
        );

        $fixtureFactory->define(Entity\Avatar::class, [
            'url' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->imageUrl();
            }),
        ]);

        $fixtureFactory->define(Entity\User::class, [
            'avatar' => FactoryBot\FieldDefinition::reference(Entity\Avatar::class),
            'id' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->uuid();
            }),
            'login' => $faker->userName(),
        ]);

        $persistingFixtureFactory = $fixtureFactory->persisting();

        $persistingFixtureFactory->createOne(Entity\User::class);

        $entityManager->flush();
    }

    public function testCreateManyDoesNotPersistEntitiesByDefault(): void
    {
        $entityManager = self::entityManager();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            $entityManager,
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => FactoryBot\FieldDefinition::sequence('name-%d'),
        ]);

        $fixtureFactory->createMany(
            Entity\Organization::class,
            FactoryBot\Count::exact(5),
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

        $fixtureFactory = new FactoryBot\FixtureFactory(
            $entityManager,
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'id' => FactoryBot\FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->unique()->uuid();
            }),
            'name' => FactoryBot\FieldDefinition::sequence('name-%d'),
        ]);

        $persistingFixtureFactory = $fixtureFactory->persisting();

        $value = $faker->numberBetween(1, 5);

        $persistingFixtureFactory->createMany(
            Entity\Organization::class,
            FactoryBot\Count::exact($value),
        );

        $entityManager->flush();
        $entityManager->clear();

        $organizations = $entityManager->getRepository(Entity\Organization::class)->findAll();

        self::assertCount($value, $organizations);
    }
}
