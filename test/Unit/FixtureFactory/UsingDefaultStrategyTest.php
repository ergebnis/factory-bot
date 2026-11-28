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

namespace Ergebnis\FactoryBot\Test\Unit\FixtureFactory;

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\FixtureFactory::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Count::class)]
#[Framework\Attributes\UsesClass(FactoryBot\EntityDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Closure::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Optional::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Reference::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\References::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Sequence::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\CountResolution\BetweenMinimumAndMaximumCount::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\FieldValueResolution\WithOrWithoutOptionalFieldValue::class)]
final class UsingDefaultStrategyTest extends FactoryBot\Test\Unit\AbstractTestCase
{
    public function testCreateOneResolvesOptionalClosureToNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::optionalClosure(static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory): Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        self::assertNull($repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::optionalClosure(static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory): Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::closure(static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory): Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::closure(static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory): Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalReferenceToNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::optionalReference(Entity\CodeOfConduct::class),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        self::assertNull($repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalReferenceToEntityWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::optionalReference(Entity\CodeOfConduct::class),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesReferenceToEntityWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::reference(Entity\CodeOfConduct::class),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesReferenceToEntityWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::reference(Entity\CodeOfConduct::class),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testCreateOneResolvesReferencesToArrayCollectionOfEntitiesWhenFakerReturnsFalseAndCountIsExact(int $value): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FactoryBot\FieldDefinition::references(
                Entity\Repository::class,
                FactoryBot\Count::exact($value),
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => self::faker()->word(),
        ]);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertContainsOnly(Entity\Repository::class, $organization->repositories());
        self::assertCount($value, $organization->repositories());
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testCreateOneResolvesReferencesToArrayCollectionOfEntitiesWhenFakerReturnsTrueAndCountIsExact(int $value): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FactoryBot\FieldDefinition::references(
                Entity\Repository::class,
                FactoryBot\Count::exact($value),
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => self::faker()->word(),
        ]);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Entity\Repository::class, $repositories);
        self::assertCount($value, $repositories);
    }

    public function testCreateOneResolvesReferencesToArrayCollectionOfEntitiesWhenFakerReturnsFalseAndCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1, 5);
        $maximum = $faker->numberBetween(10, 20);

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FactoryBot\FieldDefinition::references(
                Entity\Repository::class,
                FactoryBot\Count::between(
                    $minimum,
                    $maximum,
                ),
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => self::faker()->word(),
        ]);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertContainsOnly(Entity\Repository::class, $organization->repositories());
        self::assertGreaterThanOrEqual($minimum, \count($organization->repositories()));
        self::assertLessThanOrEqual($maximum, \count($organization->repositories()));
    }

    public function testCreateOneResolvesReferencesToArrayCollectionOfEntitiesWhenFakerReturnsTrueAndCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1, 5);
        $maximum = $faker->numberBetween(10, 20);

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FactoryBot\FieldDefinition::references(
                Entity\Repository::class,
                FactoryBot\Count::between(
                    $minimum,
                    $maximum,
                ),
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => self::faker()->word(),
        ]);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertContainsOnly(Entity\Repository::class, $organization->repositories());
        self::assertGreaterThanOrEqual($minimum, \count($organization->repositories()));
        self::assertLessThanOrEqual($maximum, \count($organization->repositories()));
    }

    public function testCreateOneResolvesOptionalSequenceToNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FactoryBot\FieldDefinition::optionalSequence('City (%d)'),
        ]);

        /** @var Entity\User $user */
        $user = $fixtureFactory->createOne(Entity\User::class);

        self::assertNull($user->location());
    }

    public function testCreateOneResolvesOptionalSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsTrue(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FactoryBot\FieldDefinition::optionalSequence('City (%d)'),
        ]);

        /** @var Entity\User $userOne */
        $userOne = $fixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $fixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $fixtureFactory->createOne(Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    public function testCreateOneResolvesSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsFalse(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FactoryBot\FieldDefinition::sequence('City (%d)'),
        ]);

        /** @var Entity\User $userOne */
        $userOne = $fixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $fixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $fixtureFactory->createOne(Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    public function testCreateOneResolvesSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsTrue(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FactoryBot\FieldDefinition::sequence('City (%d)'),
        ]);

        /** @var Entity\User $userOne */
        $userOne = $fixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $fixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $fixtureFactory->createOne(Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testCreateManyResolvesToArrayOfEntitiesWhenCountIsExact(int $value): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Entity\Organization::class,
            FactoryBot\Count::exact($value),
        );

        self::assertCount($value, $entities);
    }

    public function testCreateManyResolvesToArrayOfEntitiesWhenCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(5, 10);
        $maximum = $faker->numberBetween($minimum + 1, $minimum + 10);

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Entity\Organization::class,
            FactoryBot\Count::between(
                $minimum,
                $maximum,
            ),
        );

        self::assertGreaterThanOrEqual($minimum, \count($entities));
        self::assertLessThanOrEqual($maximum, \count($entities));
    }
}
