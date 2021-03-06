<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit\FixtureFactory;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Double;
use Ergebnis\FactoryBot\Test\Unit;
use Example\Entity;
use Faker\Generator;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\Count
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Closure
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Optional
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Reference
 * @uses \Ergebnis\FactoryBot\FieldDefinition\References
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Sequence
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\Strategy\DefaultStrategy
 * @uses \Ergebnis\FactoryBot\Strategy\WithOptionalStrategy
 */
final class UsingWithOptionalStrategyTest extends Unit\AbstractTestCase
{
    public function testCreateOneResolvesOptionalClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalClosure(static function (Generator $faker, FixtureFactory $fixtureFactory): Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Entity\CodeOfConduct::class);
            }),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Repository $repository */
        $repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalClosure(static function (Generator $faker, FixtureFactory $fixtureFactory): Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Entity\CodeOfConduct::class);
            }),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Repository $repository */
        $repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::closure(static function (Generator $faker, FixtureFactory $fixtureFactory): Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Entity\CodeOfConduct::class);
            }),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Repository $repository */
        $repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::closure(static function (Generator $faker, FixtureFactory $fixtureFactory): Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Entity\CodeOfConduct::class);
            }),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Repository $repository */
        $repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalReferenceToEntityWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalReference(Entity\CodeOfConduct::class),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Repository $repository */
        $repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalReferenceToEntityWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalReference(Entity\CodeOfConduct::class),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Repository $repository */
        $repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesReferenceToEntityWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::reference(Entity\CodeOfConduct::class),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Repository $repository */
        $repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesReferenceToEntityWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Entity\CodeOfConduct::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::reference(Entity\CodeOfConduct::class),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Repository $repository */
        $repository = $withOptionalFixtureFactory->createOne(Entity\Repository::class);

        self::assertInstanceOf(Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testCreateOneResolvesReferencesToArrayCollectionOfEntitiesWhenFakerReturnsFalseAndCountIsExact(int $value): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Entity\Repository::class,
                Count::exact($value)
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Organization $organization */
        $organization = $withOptionalFixtureFactory->createOne(Entity\Organization::class);

        self::assertContainsOnly(Entity\Repository::class, $organization->repositories());
        self::assertCount($value, $organization->repositories());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testCreateOneResolvesReferencesToArrayCollectionOfEntitiesWhenFakerReturnsTrueAndCountIsExact(int $value): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Entity\Repository::class,
                Count::exact($value)
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Organization $organization */
        $organization = $withOptionalFixtureFactory->createOne(Entity\Organization::class);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Entity\Repository::class, $repositories);
        self::assertCount($value, $repositories);
    }

    public function testCreateOneResolvesReferencesToArrayCollectionOfEntitiesWhenFakerReturnsFalseAndCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1, 5);
        $maximum = $faker->numberBetween(10, 20);

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Entity\Repository::class,
                Count::between(
                    $minimum,
                    $maximum
                )
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Organization $organization */
        $organization = $withOptionalFixtureFactory->createOne(Entity\Organization::class);

        self::assertContainsOnly(Entity\Repository::class, $organization->repositories());
        self::assertGreaterThanOrEqual($minimum, \count($organization->repositories()));
        self::assertLessThanOrEqual($maximum, \count($organization->repositories()));
    }

    public function testCreateOneResolvesReferencesToArrayCollectionOfEntitiesWhenFakerReturnsTrueAndCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1, 5);
        $maximum = $faker->numberBetween(10, 20);

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Entity\Repository::class,
                Count::between(
                    $minimum,
                    $maximum
                )
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\Organization $organization */
        $organization = $withOptionalFixtureFactory->createOne(Entity\Organization::class);

        self::assertContainsOnly(Entity\Repository::class, $organization->repositories());
        self::assertGreaterThanOrEqual($minimum, \count($organization->repositories()));
        self::assertLessThanOrEqual($maximum, \count($organization->repositories()));
    }

    public function testCreateOneResolvesOptionalSequenceToToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FieldDefinition::optionalSequence('City (%d)'),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\User $userOne */
        $userOne = $withOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $withOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $withOptionalFixtureFactory->createOne(Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    public function testCreateOneResolvesOptionalSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FieldDefinition::optionalSequence('City (%d)'),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\User $userOne */
        $userOne = $withOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $withOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $withOptionalFixtureFactory->createOne(Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    public function testCreateOneResolvesSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FieldDefinition::sequence('City (%d)'),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\User $userOne */
        $userOne = $withOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $withOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $withOptionalFixtureFactory->createOne(Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    public function testCreateOneResolvesSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Entity\User::class, [
            'location' => FieldDefinition::sequence('City (%d)'),
        ]);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        /** @var Entity\User $userOne */
        $userOne = $withOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userTwo */
        $userTwo = $withOptionalFixtureFactory->createOne(Entity\User::class);

        /** @var Entity\User $userThree */
        $userThree = $withOptionalFixtureFactory->createOne(Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testCreateManyResolvesToArrayOfEntitiesWhenCountIsExact(int $value): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Entity\Organization::class);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        $entities = $withOptionalFixtureFactory->createMany(
            Entity\Organization::class,
            Count::exact($value)
        );

        self::assertCount($value, $entities);
    }

    public function testCreateManyResolvesToNonEmptyArrayOfEntitiesWhenCountIsBetweenMinimumIsZeroAndFakerReturnsMinimum(): void
    {
        $minimum = 0;
        $maximum = 10;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\MinimumGenerator()
        );

        $fixtureFactory->define(Entity\Organization::class);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        $entities = $withOptionalFixtureFactory->createMany(
            Entity\Organization::class,
            Count::between(
                $minimum,
                $maximum
            )
        );

        self::assertCount(1, $entities);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testCreateManyResolvesToNonEmptyArrayOfEntitiesWhenCountIsBetween(int $minimum): void
    {
        $maximum = $minimum + 10;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\MinimumGenerator()
        );

        $fixtureFactory->define(Entity\Organization::class);

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        $entities = $withOptionalFixtureFactory->createMany(
            Entity\Organization::class,
            Count::between(
                $minimum,
                $maximum
            )
        );

        self::assertGreaterThanOrEqual(1, \count($entities));
        self::assertGreaterThanOrEqual($minimum, \count($entities));
        self::assertLessThanOrEqual($maximum, \count($entities));
    }
}
