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
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\FieldValueResolution\WithOrWithoutOptionalFieldValue::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\CountResolution\MinimumCount::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\FieldValueResolution\WithoutOptionalFieldValue::class)]
final class UsingWithoutOptionalStrategyTest extends FactoryBot\Test\Unit\AbstractTestCase
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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Repository $repository */
        $repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertNull($repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalClosureToNullWhenFakerReturnsTrue(): void
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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Repository $repository */
        $repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertNull($repository->codeOfConduct());
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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Repository $repository */
        $repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Repository $repository */
        $repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Repository $repository */
        $repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertNull($repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalReferenceToNullWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FactoryBot\FieldDefinition::optionalReference(Entity\CodeOfConduct::class),
        ]);

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Repository $repository */
        $repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertNull($repository->codeOfConduct());
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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Repository $repository */
        $repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Repository $repository */
        $repository = $withoutOptionalFixtureFactory->createOne(Entity\Repository::class);

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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Organization $organization */
        $organization = $withoutOptionalFixtureFactory->createOne(Entity\Organization::class);

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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Organization $organization */
        $organization = $withoutOptionalFixtureFactory->createOne(Entity\Organization::class);

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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Organization $organization */
        $organization = $withoutOptionalFixtureFactory->createOne(Entity\Organization::class);

        self::assertContainsOnly(Entity\Repository::class, $organization->repositories());
        self::assertCount($minimum, $organization->repositories());
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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\Organization $organization */
        $organization = $withoutOptionalFixtureFactory->createOne(Entity\Organization::class);

        self::assertContainsOnly(Entity\Repository::class, $organization->repositories());
        self::assertCount($minimum, $organization->repositories());
    }

    public function testCreateOneResolvesOptionalSequenceNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FactoryBot\FieldDefinition::optionalSequence('City (%d)'),
        ]);

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\User $userOne */
        $userOne = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        self::assertNull($userOne->location());
        self::assertNull($userTwo->location());
        self::assertNull($userThree->location());
    }

    public function testCreateOneResolvesOptionalSequenceNullWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FactoryBot\FieldDefinition::optionalSequence('City (%d)'),
        ]);

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\User $userOne */
        $userOne = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        self::assertNull($userOne->location());
        self::assertNull($userTwo->location());
        self::assertNull($userThree->location());
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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\User $userOne */
        $userOne = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        /** @var Entity\User $userOne */
        $userOne = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $withoutOptionalFixtureFactory->createOne(Entity\User::class);

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

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        $entities = $withoutOptionalFixtureFactory->createMany(
            Entity\Organization::class,
            FactoryBot\Count::exact($value),
        );

        self::assertCount($value, $entities);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testCreateManyResolvesToArrayOfEntitiesWhenCountIsBetween(int $minimum): void
    {
        $maximum = $minimum + 10;

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            new FactoryBot\Test\Double\Faker\MinimumGenerator(),
        );

        $fixtureFactory->define(Entity\Organization::class);

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        $entities = $withoutOptionalFixtureFactory->createMany(
            Entity\Organization::class,
            FactoryBot\Count::between(
                $minimum,
                $maximum,
            ),
        );

        self::assertCount($minimum, $entities);
        self::assertLessThanOrEqual($maximum, \count($entities));
    }
}
