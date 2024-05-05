<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\EntityDefinition;
use Ergebnis\FactoryBot\EntityMetadata;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FieldResolution;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test;
use Example\Entity;
use Example\Test\Fixture;
use Faker\Generator;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FixtureFactory::class)]
#[Framework\Attributes\UsesClass(Count::class)]
#[Framework\Attributes\UsesClass(EntityDefinition::class)]
#[Framework\Attributes\UsesClass(EntityMetadata::class)]
#[Framework\Attributes\UsesClass(Exception\ClassMetadataNotFound::class)]
#[Framework\Attributes\UsesClass(Exception\ClassNotFound::class)]
#[Framework\Attributes\UsesClass(Exception\EntityDefinitionAlreadyRegistered::class)]
#[Framework\Attributes\UsesClass(Exception\EntityDefinitionNotRegistered::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidCount::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidDefinition::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidDirectory::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidFieldNames::class)]
#[Framework\Attributes\UsesClass(FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Closure::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Optional::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Reference::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\References::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Sequence::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FieldResolution\CountResolution\BetweenMinimumAndMaximumCount::class)]
#[Framework\Attributes\UsesClass(FieldResolution\FieldValueResolution\WithOrWithoutOptionalFieldValue::class)]
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testDefineThrowsEntityDefinitionAlreadyRegisteredExceptionWhenDefinitionHasAlreadyBeenProvidedForEntity(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class);

        $this->expectException(Exception\EntityDefinitionAlreadyRegistered::class);

        $fixtureFactory->define(Entity\Organization::class);
    }

    public function testDefineThrowsClassNotFoundExceptionWhenClassDoesNotExist(): void
    {
        $className = 'NotAClass';

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $this->expectException(Exception\ClassNotFound::class);

        $fixtureFactory->define($className);
    }

    public function testDefineThrowsClassMetadataNotFoundExceptionWhenClassNameDoesNotReferenceAnEntity(): void
    {
        $className = Test\Fixture\FixtureFactory\NotAnEntity\User::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $this->expectException(Exception\ClassMetadataNotFound::class);

        $fixtureFactory->define($className);
    }

    public function testDefineThrowsInvalidFieldNamesExceptionWhenUsingFieldNamesThatDoNotExistInEntity(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->define(Entity\User::class, [
            'avatar' => new Entity\Avatar(),
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
        ]);
    }

    public function testLoadRejectsNonExistentDirectory(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $this->expectException(Exception\InvalidDirectory::class);

        $fixtureFactory->load(__DIR__ . '/../Fixture/DefinitionProvider/NonExistentDirectory');
    }

    public function testLoadThrowsInvalidDefinitionExceptionWhenDefinitionProviderCanNotBeAutoloaded(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'Definition "%s" can not be autoloaded.',
            Test\Fixture\DefinitionProvider\CanNotBeAutoloaded\RepositoryDefinitionProviderButCanNotBeAutoloaded::class,
        ));

        $fixtureFactory->load(__DIR__ . '/../Fixture/DefinitionProvider/CanNotBeAutoloaded');
    }

    public function testLoadIgnoresClassesWhichDoNotImplementDefinitionProviderInterface(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->load(__DIR__ . '/../Fixture/DefinitionProvider/DoesNotImplementDefinitionProvider');

        $fixtureFactory->createOne(Entity\Organization::class);
        $fixtureFactory->createOne(Entity\User::class);

        $this->expectException(Exception\EntityDefinitionNotRegistered::class);

        $fixtureFactory->createOne(Entity\Repository::class);
    }

    public function testLoadIgnoresClassesWhichImplementDefinitionProviderInterfaceButAreAbstract(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->load(__DIR__ . '/../Fixture/DefinitionProvider/ImplementsDefinitionProviderButIsAbstract');

        $fixtureFactory->createOne(Entity\Organization::class);
        $fixtureFactory->createOne(Entity\User::class);

        $this->expectException(Exception\EntityDefinitionNotRegistered::class);

        $fixtureFactory->createOne(Entity\Repository::class);
    }

    public function testLoadThrowsInvalidDefinitionExceptionWhenClassImplementsDefinitionProviderInterfaceButCanNotBeInstantiated(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'Definition "%s" can not be instantiated.',
            Test\Fixture\DefinitionProvider\ImplementsDefinitionProviderButCanNotBeInstantiated\RepositoryDefinitionProvider::class,
        ));

        $fixtureFactory->load(__DIR__ . '/../Fixture/DefinitionProvider/ImplementsDefinitionProviderButCanNotBeInstantiated');
    }

    public function testLoadThrowsInvalidDefinitionExceptionWhenClassImplementsDefinitionProviderInterfaceButExceptionIsThrownDuringInstantation(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'An exception was thrown while trying to instantiate definition "%s".',
            Test\Fixture\DefinitionProvider\ImplementsDefinitionProviderButThrowsExceptionDuringInstantiation\RepositoryDefinitionProvider::class,
        ));

        $fixtureFactory->load(__DIR__ . '/../Fixture/DefinitionProvider/ImplementsDefinitionProviderButThrowsExceptionDuringInstantiation');
    }

    public function testLoadAcceptsClassesWhichImplementDefinitionProviderInterfaceAndHaveNoIssues(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->load(__DIR__ . '/../../example/test/Fixture/Entity');

        $organization = $fixtureFactory->createOne(Entity\Organization::class);
        $repository = $fixtureFactory->createOne(Entity\Repository::class);
        $user = $fixtureFactory->createOne(Entity\User::class);

        self::assertInstanceOf(Entity\Organization::class, $organization);
        self::assertInstanceOf(Entity\Repository::class, $repository);
        self::assertInstanceOf(Entity\User::class, $user);
    }

    public function testRegisterAcceptsDefinitionProviders(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->register(
            new Fixture\Entity\AvatarDefinitionProvider(),
            new Fixture\Entity\CodeOfConductDefinitionProvider(),
            new Fixture\Entity\OrganizationDefinitionProvider(),
            new Fixture\Entity\RepositoryDefinitionProvider(),
            new Fixture\Entity\UserDefinitionProvider(),
        );

        $organization = $fixtureFactory->createOne(Entity\Organization::class);
        $repository = $fixtureFactory->createOne(Entity\Repository::class);
        $user = $fixtureFactory->createOne(Entity\User::class);

        self::assertInstanceOf(Entity\Organization::class, $organization);
        self::assertInstanceOf(Entity\Repository::class, $repository);
        self::assertInstanceOf(Entity\User::class, $user);
    }

    public function testCreateOneThrowsEntityDefinitionNotRegisteredWhenEntityDefinitionHasNotBeenRegistered(): void
    {
        $className = self::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $this->expectException(Exception\EntityDefinitionNotRegistered::class);

        $fixtureFactory->createOne($className);
    }

    public function testCreateOneThrowsInvalidFieldNamesExceptionWhenReferencingFieldsThatDoNotExistInEntity(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => $faker->word(),
        ]);

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->createOne(Entity\Organization::class, [
            'flavour' => 'strawberry',
        ]);
    }

    public function testDefineAllowsDefiningAndReferencingEmbeddables(): void
    {
        $faker = self::faker();

        $url = $faker->imageUrl();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Avatar::class, [
            'url' => $url,
        ]);

        $fixtureFactory->define(Entity\User::class, [
            'avatar' => FieldDefinition::reference(Entity\Avatar::class),
        ]);

        $user = $fixtureFactory->createOne(Entity\User::class);

        self::assertInstanceOf(Entity\User::class, $user);

        /** @var Entity\User $user */
        $avatar = $user->avatar();

        self::assertSame($url, $avatar->url());
    }

    public function testDefineThrowsInvalidFieldNamesExceptionWhenReferencingFieldsOfEmbeddablesUsingDotNotation(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->define(Entity\User::class, [
            'login' => $faker->userName(),
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
            $faker,
        );

        $fixtureFactory->define(Entity\User::class);

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->createOne(Entity\User::class, [
            'login' => $faker->userName(),
            'avatar.url' => $faker->imageUrl(),
            'avatar.width' => $faker->numberBetween(100, 250),
            'avatar.height' => $faker->numberBetween(100, 250),
        ]);
    }

    public function testDefineAcceptsConstantValuesInEntityDefinitions(): void
    {
        $faker = self::faker();

        $name = $faker->word();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => $name,
        ]);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertSame($name, $organization->name());
    }

    public function testDefineAcceptsClosureInEntityDefinitions(): void
    {
        $name = 'foo';

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => static function () use (&$name): string {
                return \sprintf(
                    'the-%s-organization',
                    $name,
                );
            },
        ]);

        /** @var Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->createOne(Entity\Organization::class);

        $name = 'bar';

        /** @var Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertSame('the-foo-organization', $organizationOne->name());
        self::assertSame('the-bar-organization', $organizationTwo->name());
    }

    public function testCreateOneResolvesFieldWithoutDefaultValueToNullWhenFieldDefinitionWasNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertNull($organization->url());
    }

    public function testCreateOneResolvesFieldWithDefaultValueToItsDefaultValueWhenFieldDefinitionWasNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertFalse($organization->isVerified());
    }

    public function testCreateOneDoesNotInvokeEntityConstructor(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class, []);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertFalse($organization->constructorWasCalled());
    }

    public function testCreateOneResolvesOneToManyAssociationToEmptyArrayCollectionWhenFieldDefinitionWasNotSpecified(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => $faker->word(),
        ]);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        self::assertEmpty($organization->repositories());
    }

    public function testCreateOneMapsArraysToCollectionAssociationFields(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Entity\Organization::class),
        ]);

        /** @var Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->createOne(Entity\Repository::class);

        /** @var Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->createOne(Entity\Repository::class);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class, [
            'name' => $faker->word(),
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
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Entity\Organization::class),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class);

        $organization = $repository->organization();

        self::assertContains($repository, $organization->repositories());
    }

    public function testOptionalFieldValuesAreSetToNullWhenFakerReturnsFalse(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            new Test\Double\Faker\FalseGenerator(),
        );

        $fixtureFactory->define(
            Entity\User::class,
            [
                'location' => FieldDefinition::optionalSequence('City (%d)'),
            ],
            static function (Entity\User $user, array $fieldValues): void {
                $fieldName = 'location';

                self::assertArrayHasKey($fieldName, $fieldValues, \sprintf(
                    'Failed asserting that key for field "%s" exists in field values.',
                    $fieldName,
                ));
            },
        );

        /** @var Entity\User $user */
        $user = $fixtureFactory->createOne(Entity\User::class);

        self::assertNull($user->location());
    }

    public function testCreateOneAllowsOverridingFieldWithDifferentValueWhenFieldDefinitionOverrideHasBeenSpecifiedAsString(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => $faker->unique()->word(),
        ]);

        $name = $faker->unique()->word();

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class, [
            'name' => $name,
        ]);

        self::assertSame($name, $organization->name());
    }

    public function testCreateOneAllowsOverridingFieldWithDifferentValueWhenFieldDefinitionOverrideHasBeenSpecifiedAsFieldDefinition(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'name' => $faker->unique()->word(),
        ]);

        $name = $faker->unique()->word();

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class, [
            'name' => FieldDefinition::value($name),
        ]);

        self::assertSame($name, $organization->name());
    }

    public function testCreateOneAllowsOverridingAssociationWithNullWhenFieldDefinitionOverrideHasBeenSpecifiedAsNull(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Repository::class, [
            'template' => FieldDefinition::references(
                Entity\Repository::class,
                Count::between(0, 5),
            ),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class, [
            'template' => null,
        ]);

        self::assertNull($repository->template());
    }

    public function testCreateOneAllowsOverridingAssociationWithNullWhenFieldDefinitionOverrideHasBeenSpecifiedAsFieldDefinition(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Repository::class, [
            'template' => FieldDefinition::references(
                Entity\Repository::class,
                Count::between(0, 5),
            ),
        ]);

        /** @var Entity\Repository $repository */
        $repository = $fixtureFactory->createOne(Entity\Repository::class, [
            'template' => FieldDefinition::value(null),
        ]);

        self::assertNull($repository->template());
    }

    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testCreateOneAllowsOverridingAssociationWithCollectionOfEntitiesWhenFieldDefinitionOverrideHasBeenSpecifiedAsArray(int $value): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Entity\Repository::class,
                Count::between(0, 5),
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => $faker->word(),
        ]);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class, [
            'repositories' => $fixtureFactory->createMany(
                Entity\Repository::class,
                Count::exact($value),
            ),
        ]);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Entity\Repository::class, $repositories);
        self::assertCount($value, $repositories);
    }

    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testCreateOneAllowsOverridingAssociationWithCollectionOfEntitiesWhenFieldDefinitionOverrideHasBeenSpecifiedAsFieldDefinition(int $value): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Entity\Repository::class,
                Count::between(0, 5),
            ),
        ]);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => $faker->word(),
        ]);

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Entity\Repository::class,
                Count::exact($value),
            ),
        ]);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Entity\Repository::class, $repositories);
        self::assertCount($value, $repositories);
    }

    public function testDefineAcceptsClosureThatWillBeInvokedAfterEntityCreation(): void
    {
        $faker = self::faker();

        $name = $faker->word();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(
            Entity\Organization::class,
            [
                'name' => $name,
            ],
            static function (Entity\Organization $organization, array $fieldValues, Generator $faker): void {
                $name = \sprintf(
                    '%s-%s-%d',
                    $organization->name(),
                    $fieldValues['name'],
                    $faker->numberBetween(10, 99),
                );

                $organization->renameTo($name);
            },
        );

        /** @var Entity\Organization $organization */
        $organization = $fixtureFactory->createOne(Entity\Organization::class);

        $expectedPattern = \sprintf(
            '/^%s-%s-\d{2}$/',
            $name,
            $name,
        );

        $actualName = $organization->name();

        self::assertMatchesRegularExpression($expectedPattern, $actualName);
    }

    public function testCreateOneCreatesReferencedObjectAutomatically(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\Organization::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'name' => $faker->word(),
            'organization' => FieldDefinition::reference(Entity\Organization::class),
        ]);

        /** @var Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->createOne(Entity\Repository::class);

        /** @var Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->createOne(Entity\Repository::class);

        $organizationOne = $repositoryOne->organization();
        $organizationTwo = $repositoryTwo->organization();

        self::assertNotNull($organizationOne);
        self::assertNotNull($organizationTwo);
        self::assertNotSame($organizationOne, $organizationTwo);
    }

    public function testCreateOneCreatesReferencedObjectsTransitively(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->define(Entity\Organization::class);

        $fixtureFactory->define(Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Entity\Organization::class),
        ]);

        $fixtureFactory->define(Entity\Project::class, [
            'repository' => FieldDefinition::reference(Entity\Repository::class),
        ]);

        $project = $fixtureFactory->createOne(Entity\Project::class);

        self::assertInstanceOf(Entity\Project::class, $project);

        /** @var Entity\Project $project */
        $repository = $project->repository();

        self::assertInstanceOf(Entity\Repository::class, $repository);

        $organization = $repository->organization();

        self::assertInstanceOf(Entity\Organization::class, $organization);
    }

    public function testCreateManyThrowsEntityDefinitionNotRegisteredWhenEntityDefinitionHasNotBeenRegistered(): void
    {
        $className = self::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $this->expectException(Exception\EntityDefinitionNotRegistered::class);

        $fixtureFactory->createMany(
            $className,
            Count::exact(3),
        );
    }

    public function testCreateManyResolvesToArrayOfEntitiesWhenFieldDefinitionOverridesAreSpecifiedAsValue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $value = $faker->numberBetween(1, 5);

        $fixtureFactory->define(Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Entity\Organization::class,
            Count::exact($value),
            [
                'isVerified' => true,
            ],
        );

        self::assertCount($value, $entities);

        $verifiedEntities = \array_filter($entities, static function (Entity\Organization $organization): bool {
            return $organization->isVerified();
        });

        self::assertCount($value, $verifiedEntities);
    }

    public function testCreateManyResolvesToArrayOfEntitiesWhenFieldDefinitionOverridesAreSpecifiedAsFieldDefinition(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $value = $faker->numberBetween(1, 5);

        $fixtureFactory->define(Entity\Organization::class);

        $entities = $fixtureFactory->createMany(
            Entity\Organization::class,
            Count::exact($value),
            [
                'isVerified' => FieldDefinition::value(true),
            ],
        );

        self::assertCount($value, $entities);

        $verifiedEntities = \array_filter($entities, static function (Entity\Organization $organization): bool {
            return $organization->isVerified();
        });

        self::assertCount($value, $verifiedEntities);
    }

    public function testWithOptionalReturnsMutatedFixtureFactory(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $withOptionalFixtureFactory = $fixtureFactory->withOptional();

        self::assertInstanceOf(FixtureFactory::class, $withOptionalFixtureFactory);
        self::assertNotSame($fixtureFactory, $withOptionalFixtureFactory);
    }

    public function testWithoutOptionalReturnsMutatedFixtureFactory(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $withoutOptionalFixtureFactory = $fixtureFactory->withoutOptional();

        self::assertInstanceOf(FixtureFactory::class, $withoutOptionalFixtureFactory);
        self::assertNotSame($fixtureFactory, $withoutOptionalFixtureFactory);
    }

    public function testPersistingReturnsMutatedFixtureFactory(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $persistingFixtureFactory = $fixtureFactory->persisting();

        self::assertInstanceOf(FixtureFactory::class, $persistingFixtureFactory);
        self::assertNotSame($fixtureFactory, $persistingFixtureFactory);
    }
}
