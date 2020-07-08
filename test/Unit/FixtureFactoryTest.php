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

namespace Ergebnis\FactoryBot\Test\Unit;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Double;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\Count
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\ClassMetadataNotFound
 * @uses \Ergebnis\FactoryBot\Exception\ClassNotFound
 * @uses \Ergebnis\FactoryBot\Exception\EntityDefinitionAlreadyRegistered
 * @uses \Ergebnis\FactoryBot\Exception\EntityDefinitionNotRegistered
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\Exception\InvalidFieldNames
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Closure
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Optional
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Reference
 * @uses \Ergebnis\FactoryBot\FieldDefinition\References
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Sequence
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testDefineThrowsEntityDefinitionAlreadyRegisteredExceptionWhenDefinitionHasAlreadyBeenProvidedForEntity(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $this->expectException(Exception\EntityDefinitionAlreadyRegistered::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);
    }

    public function testDefineThrowsClassNotFoundExceptionWhenClassDoesNotExist(): void
    {
        $className = 'NotAClass';

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $this->expectException(Exception\ClassNotFound::class);

        $fixtureFactory->define($className);
    }

    public function testDefineThrowsClassMetadataNotFoundExceptionWhenClassNameDoesNotReferenceAnEntity(): void
    {
        $className = Fixture\FixtureFactory\NotAnEntity\User::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $this->expectException(Exception\ClassMetadataNotFound::class);

        $fixtureFactory->define($className);
    }

    public function testDefineThrowsInvalidFieldNamesExceptionWhenUsingFieldNamesThatDoNotExistInEntity(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class, [
            'avatar' => new Fixture\FixtureFactory\Entity\Avatar(),
            'email' => $faker->email,
            'phone' => $faker->phoneNumber,
        ]);
    }

    public function testCreateOneThrowsEntityDefinitionNotRegisteredWhenEntityDefinitionHasNotBeenRegistered(): void
    {
        $className = self::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $this->expectException(Exception\EntityDefinitionNotRegistered::class);

        $fixtureFactory->createOne($className);
    }

    public function testCreateOneThrowsInvalidFieldNamesExceptionWhenReferencingFieldsThatDoNotExistInEntity(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class, [
            'flavour' => 'strawberry',
        ]);
    }

    public function testDefineAllowsDefiningAndReferencingEmbeddables(): void
    {
        $faker = self::faker();

        $url = $faker->imageUrl();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Avatar::class, [
            'url' => $url,
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class, [
            'avatar' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Avatar::class),
        ]);

        $user = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\User::class, $user);

        /** @var Fixture\FixtureFactory\Entity\User $user */
        $avatar = $user->avatar();

        self::assertSame($url, $avatar->url());
    }

    public function testDefineThrowsInvalidFieldNamesExceptionWhenReferencingFieldsOfEmbeddablesUsingDotNotation(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class, [
            'login' => $faker->userName,
            'avatar.url' => $faker->imageUrl(),
            'avatar.width' => $faker->numberBetween(100, 250),
            'avatar.height' => $faker->numberBetween(100, 250),
        ]);
    }

    public function testCreateOneThrowsInvalidFieldNamesExceptionWhenReferencingFieldsOfEmbeddablesUsingDotNotation(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class);

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class, [
            'login' => $faker->userName,
            'avatar.url' => $faker->imageUrl(),
            'avatar.width' => $faker->numberBetween(100, 250),
            'avatar.height' => $faker->numberBetween(100, 250),
        ]);
    }

    public function testDefineAcceptsConstantValuesInEntityDefinitions(): void
    {
        $faker = self::faker();

        $name = $faker->word;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame($name, $organization->name());
    }

    public function testDefineAcceptsClosureInEntityDefinitions(): void
    {
        $name = 'foo';

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => static function () use (&$name): string {
                return \sprintf(
                    'the-%s-organization',
                    $name
                );
            },
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $name = 'bar';

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame('the-foo-organization', $organizationOne->name());
        self::assertSame('the-bar-organization', $organizationTwo->name());
    }

    public function testCreateOneResolvesFieldWithoutDefaultValueToNullWhenFieldDefinitionWasNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertNull($organization->name());
    }

    public function testCreateOneResolvesFieldWithDefaultValueToItsDefaultValueWhenFieldDefinitionWasNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertFalse($organization->isVerified());
    }

    public function testCreateOneDoesNotInvokeEntityConstructor(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, []);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertFalse($organization->constructorWasCalled());
    }

    public function testCreateOneResolvesOneToManyAssociationToEmptyArrayCollectionWhenFieldDefinitionWasNotSpecified(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertEmpty($organization->repositories());
    }

    public function testCreateOneMapsArraysToCollectionAsscociationFields(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
            'repositories' => [
                $repositoryOne,
                $repositoryTwo,
            ],
        ]);

        self::assertContains($repositoryOne, $organization->repositories());
        self::assertContains($repositoryTwo, $organization->repositories());
    }

    public function testCreateOneEstablishesBidirectionalOneToManyAssociations(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        $organization = $repository->organization();

        self::assertContains($repository, $organization->repositories());
    }

    public function testCreateOneResolvesOptionalClosureToNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalClosure(static function (FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertNull($repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalClosureToResultOfClosureInvokedWithFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalClosure(static function (FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesRequiredClosureToResultOfClosureInvokedWithFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::closure(static function (FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesRequiredClosureToResultOfClosureInvokedWithFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::closure(static function (FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalReferenceToNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalReference(Fixture\FixtureFactory\Entity\CodeOfConduct::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertNull($repository->codeOfConduct());
    }

    public function testCreateOneResolvesOptionalReferenceToEntityWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalReference(Fixture\FixtureFactory\Entity\CodeOfConduct::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesRequiredReferenceToEntityWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\CodeOfConduct::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesRequiredReferenceToEntityWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\CodeOfConduct::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $value
     */
    public function testCreateOneResolvesRequiredReferencesToArrayCollectionOfEntitiesWhenFakerReturnsFalse(int $value): void
    {
        $count = new Count($value);

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                $count
            ),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $organization->repositories());
        self::assertCount($count->value(), $organization->repositories());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $value
     */
    public function testCreateOneResolvesRequiredReferencesToArrayCollectionOfEntitiesWhenFakerReturnsTrue(int $value): void
    {
        $count = new Count($value);

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                $count
            ),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($count->value(), $repositories);
    }

    public function testCreateOneResolvesOptionalSequenceNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class, [
            'location' => FieldDefinition::optionalSequence('City (%d)'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\User $user */
        $user = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        self::assertNull($user->location());
    }

    public function testCreateOneResolvesOptionalSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class, [
            'location' => FieldDefinition::optionalSequence('City (%d)'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\User $userOne */
        $userOne = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        /** @var Fixture\FixtureFactory\Entity\User $userTwo */
        $userTwo = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        /** @var Fixture\FixtureFactory\Entity\User $userThree */
        $userThree = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    public function testCreateOneResolvesRequiredSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class, [
            'location' => FieldDefinition::sequence('City (%d)'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\User $userOne */
        $userOne = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        /** @var Fixture\FixtureFactory\Entity\User $userTwo */
        $userTwo = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        /** @var Fixture\FixtureFactory\Entity\User $userThree */
        $userThree = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    public function testCreateOneResolvesRequiredSequenceToStringValueWhenPercentDPlaceholderIsPresentAndFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\User::class, [
            'location' => FieldDefinition::sequence('City (%d)'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\User $userOne */
        $userOne = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        /** @var Fixture\FixtureFactory\Entity\User $userTwo */
        $userTwo = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        /** @var Fixture\FixtureFactory\Entity\User $userThree */
        $userThree = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        self::assertSame('City (1)', $userOne->location());
        self::assertSame('City (2)', $userTwo->location());
        self::assertSame('City (3)', $userThree->location());
    }

    public function testCreateOneAllowsOverridingFieldWithDifferentValueWhenFieldDefinitionHasBeenSpecified(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->unique()->word,
        ]);

        $name = $faker->unique()->word;

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        self::assertSame($name, $organization->name());
    }

    public function testCreateOneAllowsOverridingAssociationWithNullWhenFieldDefinitionHasBeenSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => FieldDefinition::references(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => null,
        ]);

        self::assertNull($repository->template());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $value
     */
    public function testCreateOneAllowsOverridingAssociationWithCollectionOfEntitiesWhenFieldDefinitionHasBeenSpecified(int $value): void
    {
        $count = new Count($value);

        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => $faker->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => $fixtureFactory->createMany(
                Fixture\FixtureFactory\Entity\Repository::class,
                $count
            ),
        ]);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($count->value(), $repositories);
    }

    public function testDefineAcceptsClosureThatWillBeInvokedAfterEntityCreation(): void
    {
        $faker = self::faker();

        $name = $faker->word;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(
            Fixture\FixtureFactory\Entity\Organization::class,
            [
                'name' => $name,
            ],
            static function (Fixture\FixtureFactory\Entity\Organization $organization, array $fieldValues): void {
                $name = \sprintf(
                    '%s-%s',
                    $organization->name(),
                    $fieldValues['name']
                );

                $organization->renameTo($name);
            }
        );

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $expectedName = \sprintf(
            '%s-%s',
            $name,
            $name
        );

        self::assertSame($expectedName, $organization->name());
    }

    public function testCreateOneCreatesReferencedObjectAutomatically(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        $organizationOne = $repositoryOne->organization();
        $organizationTwo = $repositoryTwo->organization();

        self::assertNotNull($organizationOne);
        self::assertNotNull($organizationTwo);
        self::assertNotSame($organizationOne, $organizationTwo);
    }

    public function testCreateOnesCreatesReferencedObjectsTransitively(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Project::class, [
            'repository' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $project = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Project::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Project::class, $project);

        /** @var Fixture\FixtureFactory\Entity\Project $project */
        $repository = $project->repository();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Repository::class, $repository);

        $organization = $repository->organization();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Organization::class, $organization);
    }

    public function testCreateManyResolvesToArrayOfEntitiesWhenCountIsNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $entities = $fixtureFactory->createMany(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertCount(1, $entities);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $value
     */
    public function testCreateManyResolvesToArrayOfEntitiesWhenCountIsSpecified(int $value): void
    {
        $count = new Count($value);

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Fixture\FixtureFactory\Entity\Organization::class,
            $count
        );

        self::assertCount($count->value(), $entities);
    }
}
