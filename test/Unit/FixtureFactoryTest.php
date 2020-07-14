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
use Faker\Generator;

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
 * @uses \Ergebnis\FactoryBot\Exception\InvalidDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidDirectory
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
    public function testDefaults(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        self::assertCount(0, $fixtureFactory->definitions());
    }

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

    public function testLoadRejectsNonExistentDirectory(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $this->expectException(Exception\InvalidDirectory::class);

        $fixtureFactory->load(__DIR__ . '/../Fixture/Definitions/NonExistentDirectory');
    }

    public function testLoadThrowsInvalidDefinitionExceptionWhenDefinitionCanNotBeAutoloaded(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'Definition "%s" can not be autoloaded.',
            Fixture\Definitions\CanNotBeAutoloaded\RepositoryDefinitionButCanNotBeAutoloaded::class
        ));

        $fixtureFactory->load(__DIR__ . '/../Fixture/Definitions/CanNotBeAutoloaded');
    }

    public function testLoadIgnoresClassesWhichDoNotImplementDefinitionInterface(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->load(__DIR__ . '/../Fixture/Definitions/DoesNotImplementDefinition');

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);
        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        $this->expectException(Exception\EntityDefinitionNotRegistered::class);

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);
    }

    public function testLoadIgnoresClassesWhichImplementDefinitionInterfaceButAreAbstract(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->load(__DIR__ . '/../Fixture/Definitions/ImplementsDefinitionButIsAbstract');

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);
        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        $this->expectException(Exception\EntityDefinitionNotRegistered::class);

        $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);
    }

    public function testLoadThrowsInvalidDefinitionExceptionWhenClassImplementsDefinitionInterfaceButCanNotBeInstantiated(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'Definition "%s" can not be instantiated.',
            Fixture\Definitions\ImplementsDefinitionButCanNotBeInstantiated\RepositoryDefinition::class
        ));

        $fixtureFactory->load(__DIR__ . '/../Fixture/Definitions/ImplementsDefinitionButCanNotBeInstantiated');
    }

    public function testLoadThrowsInvalidDefinitionExceptionWhenClassImplementsDefinitionInterfaceButExceptionIsThrownDuringInstantation(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'An exception was thrown while trying to instantiate definition "%s".',
            Fixture\Definitions\ImplementsDefinitionButThrowsExceptionDuringInstantiation\RepositoryDefinition::class
        ));

        $fixtureFactory->load(__DIR__ . '/../Fixture/Definitions/ImplementsDefinitionButThrowsExceptionDuringInstantiation');
    }

    public function testLoadAcceptsClassesWhichImplementDefinitionInterfaceAndHaveNoIssues(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->load(__DIR__ . '/../Fixture/Definitions/ImplementsDefinition');

        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);
        $user = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Organization::class, $organization);
        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Repository::class, $repository);
        self::assertInstanceOf(Fixture\FixtureFactory\Entity\User::class, $user);
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
            $faker
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

    public function testCreateOneMapsArraysToCollectionAssociationFields(): void
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

    public function testCreateOneResolvesOptionalClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::optionalClosure(static function (Generator $faker, FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesRequiredClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::closure(static function (Generator $faker, FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\CodeOfConduct {
                return $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\CodeOfConduct::class);
            }),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\CodeOfConduct::class, $repository->codeOfConduct());
    }

    public function testCreateOneResolvesRequiredClosureToResultOfClosureInvokedWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\CodeOfConduct::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'codeOfConduct' => FieldDefinition::closure(static function (Generator $faker, FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\CodeOfConduct {
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
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testCreateOneResolvesRequiredReferencesToArrayCollectionOfEntitiesWhenFakerReturnsFalseAndCountIsExact(int $value): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::exact($value)
            ),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $organization->repositories());
        self::assertCount($value, $organization->repositories());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testCreateOneResolvesRequiredReferencesToArrayCollectionOfEntitiesWhenFakerReturnsTrueAndCountIsExact(int $value): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::exact($value)
            ),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($value, $repositories);
    }

    public function testCreateOneResolvesRequiredReferencesToArrayCollectionOfEntitiesWhenFakerReturnsFalseAndCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1, 5);
        $maximum = $faker->numberBetween(10, 20);

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::between(
                    $minimum,
                    $maximum
                )
            ),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $organization->repositories());
        self::assertGreaterThanOrEqual($minimum, \count($organization->repositories()));
        self::assertLessThanOrEqual($maximum, \count($organization->repositories()));
    }

    public function testCreateOneResolvesRequiredReferencesToArrayCollectionOfEntitiesWhenFakerReturnsTrueAndCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(1, 5);
        $maximum = $faker->numberBetween(10, 20);

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\TrueGenerator()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::between(
                    $minimum,
                    $maximum
                )
            ),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $organization->repositories());
        self::assertGreaterThanOrEqual($minimum, \count($organization->repositories()));
        self::assertLessThanOrEqual($maximum, \count($organization->repositories()));
    }

    public function testCreateOneResolvesOptionalSequenceToNullWhenFakerReturnsFalse(): void
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

    public function testOptionalFieldValuesAreSetToNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Double\Faker\FalseGenerator()
        );

        $fixtureFactory->define(
            Fixture\FixtureFactory\Entity\User::class,
            [
                'location' => FieldDefinition::optionalSequence('City (%d)'),
            ],
            static function (Fixture\FixtureFactory\Entity\User $user, array $fieldValues): void {
                $fieldName = 'location';

                self::assertArrayHasKey($fieldName, $fieldValues, \sprintf(
                    'Failed asserting that key for field "%s" exists in field values.',
                    $fieldName
                ));
            }
        );

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

    public function testCreateOneAllowsOverridingFieldWithDifferentValueWhenFieldDefinitionOverrideHasBeenSpecifiedAsString(): void
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

    public function testCreateOneAllowsOverridingFieldWithDifferentValueWhenFieldDefinitionOverrideHasBeenSpecifiedAsFieldDefinition(): void
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
            'name' => FieldDefinition::value($name),
        ]);

        self::assertSame($name, $organization->name());
    }

    public function testCreateOneAllowsOverridingAssociationWithNullWhenFieldDefinitionOverrideHasBeenSpecifiedAsNull(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::between(0, 5)
            ),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => null,
        ]);

        self::assertNull($repository->template());
    }

    public function testCreateOneAllowsOverridingAssociationWithNullWhenFieldDefinitionOverrideHasBeenSpecifiedAsFieldDefinition(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::between(0, 5)
            ),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => FieldDefinition::value(null),
        ]);

        self::assertNull($repository->template());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testCreateOneAllowsOverridingAssociationWithCollectionOfEntitiesWhenFieldDefinitionOverrideHasBeenSpecifiedAsArray(int $value): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::between(0, 5)
            ),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => $faker->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => $fixtureFactory->createMany(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::exact($value)
            ),
        ]);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($value, $repositories);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testCreateOneAllowsOverridingAssociationWithCollectionOfEntitiesWhenFieldDefinitionOverrideHasBeenSpecifiedAsFieldDefinition(int $value): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::between(0, 5)
            ),
        ]);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => $faker->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                Count::exact($value)
            ),
        ]);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($value, $repositories);
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
            static function (Fixture\FixtureFactory\Entity\Organization $organization, array $fieldValues, Generator $faker): void {
                $name = \sprintf(
                    '%s-%s-%d',
                    $organization->name(),
                    $fieldValues['name'],
                    $faker->numberBetween(10, 99)
                );

                $organization->renameTo($name);
            }
        );

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\Organization::class);

        $expectedPattern = \sprintf(
            '/^%s-%s-\d{2}$/',
            $name,
            $name
        );

        $actualName = $organization->name();

        self::assertIsString($actualName);
        self::assertRegExp($expectedPattern, $actualName);
    }

    public function testCreateOneCreatesReferencedObjectAutomatically(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => $faker->word,
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

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testCreateManyResolvesToArrayOfEntitiesWhenCountIsExact(int $value): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Fixture\FixtureFactory\Entity\Organization::class,
            Count::exact($value)
        );

        self::assertCount($value, $entities);
    }

    public function testCreateManyResolvesToArrayOfEntitiesWhenCountIsBetween(): void
    {
        $faker = self::faker();

        $minimum = $faker->numberBetween(5, 10);
        $maximum = $faker->numberBetween($minimum + 1, $minimum + 10);

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Fixture\FixtureFactory\Entity\Organization::class,
            Count::between(
                $minimum,
                $maximum
            )
        );

        self::assertGreaterThanOrEqual($minimum, \count($entities));
        self::assertLessThanOrEqual($maximum, \count($entities));
    }

    public function testCreateManyResolvesToArrayOfEntitiesWhenFieldDefinitionOverridesAreSpecifiedAsValue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $value = $faker->numberBetween(1, 5);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Fixture\FixtureFactory\Entity\Organization::class,
            Count::exact($value),
            [
                'isVerified' => true,
            ]
        );

        self::assertCount($value, $entities);

        $verifiedEntities = \array_filter($entities, static function (Fixture\FixtureFactory\Entity\Organization $organization): bool {
            return $organization->isVerified();
        });

        self::assertCount($value, $verifiedEntities);
    }

    public function testCreateManyResolvesToArrayOfEntitiesWhenFieldDefinitionOverridesAreSpecifiedAsFieldDefinition(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $value = $faker->numberBetween(1, 5);

        $fixtureFactory->define(Fixture\FixtureFactory\Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Fixture\FixtureFactory\Entity\Organization::class,
            Count::exact($value),
            [
                'isVerified' => FieldDefinition::value(true),
            ]
        );

        self::assertCount($value, $entities);

        $verifiedEntities = \array_filter($entities, static function (Fixture\FixtureFactory\Entity\Organization $organization): bool {
            return $organization->isVerified();
        });

        self::assertCount($value, $verifiedEntities);
    }
}
