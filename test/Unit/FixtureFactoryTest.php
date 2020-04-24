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

use Doctrine\ORM;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\ClassMetadataNotFound
 * @uses \Ergebnis\FactoryBot\Exception\ClassNotFound
 * @uses \Ergebnis\FactoryBot\Exception\EntityDefinitionAlreadyRegistered
 * @uses \Ergebnis\FactoryBot\Exception\EntityDefinitionNotRegistered
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\Exception\InvalidFieldNames
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Closure
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Reference
 * @uses \Ergebnis\FactoryBot\FieldDefinition\References
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Sequence
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    public function testDefineEntityThrowsEntityDefinitionAlreadyRegisteredExceptionWhenDefinitionHasAlreadyBeenProvidedForEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $this->expectException(Exception\EntityDefinitionAlreadyRegistered::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);
    }

    public function testDefineEntityThrowsClassNotFoundExceptionWhenClassDoesNotExist(): void
    {
        $className = 'NotAClass';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $this->expectException(Exception\ClassNotFound::class);

        $fixtureFactory->defineEntity($className);
    }

    public function testDefineEntityThrowsClassMetadataNotFoundExceptionWhenClassNameDoesNotReferenceAnEntity(): void
    {
        $className = Fixture\FixtureFactory\NotAnEntity\User::class;

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $this->expectException(Exception\ClassMetadataNotFound::class);

        $fixtureFactory->defineEntity($className);
    }

    public function testDefineEntityThrowsInvalidFieldNamesExceptionWhenUsingFieldNamesThatDoNotExistInEntity(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class, [
            'avatar' => new Fixture\FixtureFactory\Entity\Avatar(),
            'email' => $faker->email,
            'phone' => $faker->phoneNumber,
        ]);
    }

    public function testCreateThrowsEntityDefinitionNotRegisteredWhenEntityDefinitionHasNotBeenRegistered(): void
    {
        $className = self::class;

        $entityManager = $this->prophesize(ORM\EntityManagerInterface::class)->reveal();

        $fixtureFactory = new FixtureFactory($entityManager);

        $this->expectException(Exception\EntityDefinitionNotRegistered::class);

        $fixtureFactory->create($className);
    }

    public function testCreateThrowsInvalidFieldNamesExceptionWhenReferencingFieldsThatDoNotExistInEntity(): void
    {
        $faker = self::faker()->unique();

        $fieldName = $faker->word;

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class, [
            $fieldName => 'blueberry',
        ]);
    }

    public function testDefineEntityAllowsDefiningAndReferencingEmbeddables(): void
    {
        $url = self::faker()->imageUrl();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Avatar::class, [
            'url' => $url,
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class, [
            'avatar' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Avatar::class),
        ]);

        $user = $fixtureFactory->create(Fixture\FixtureFactory\Entity\User::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\User::class, $user);

        /** @var Fixture\FixtureFactory\Entity\User $user */
        $avatar = $user->avatar();

        self::assertSame($url, $avatar->url());
    }

    public function testDefineEntityThrowsInvalidFieldNamesExceptionWhenReferencingFieldsOfEmbeddablesUsingDotNotation(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class, [
            'login' => $faker->userName,
            'avatar.url' => $faker->imageUrl(),
            'avatar.width' => $faker->numberBetween(100, 250),
            'avatar.height' => $faker->numberBetween(100, 250),
        ]);
    }

    public function testCreateThrowsInvalidFieldNamesExceptionWhenReferencingFieldsOfEmbeddablesUsingDotNotation(): void
    {
        $faker = self::faker()->unique();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $this->expectException(Exception\InvalidFieldNames::class);

        $fixtureFactory->create(Fixture\FixtureFactory\Entity\User::class, [
            'login' => $faker->userName,
            'avatar.url' => $faker->imageUrl(),
            'avatar.width' => $faker->numberBetween(100, 250),
            'avatar.height' => $faker->numberBetween(100, 250),
        ]);
    }

    public function testDefineEntityAcceptsConstantValuesInEntityDefinitions(): void
    {
        $name = self::faker()->word;

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame($name, $organization->name());
    }

    public function testDefineEntityAcceptsClosureInEntityDefinitions(): void
    {
        $name = 'foo';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => static function () use (&$name): string {
                return \sprintf(
                    'the-%s-organization',
                    $name
                );
            },
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        $name = 'bar';

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame('the-foo-organization', $organizationOne->name());
        self::assertSame('the-bar-organization', $organizationTwo->name());
    }

    public function testCreateResolvesFieldWithoutDefaultValueToNullWhenFieldDefinitionWasNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertNull($organization->name());
    }

    public function testCreateResolvesFieldWithDefaultValueToItsDefaultValueWhenFieldDefinitionWasNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertFalse($organization->isVerified());
    }

    public function testCreateDoesNotInvokeEntityConstructor(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, []);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertFalse($organization->constructorWasCalled());
    }

    public function testCreateAssignsEmptyCollectionToCollectionAssociationWhenFieldDefinitionWasNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertEmpty($organization->repositories());
    }

    public function testCreateMapsArraysToCollectionAsscociationFields(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => self::faker()->word,
            'repositories' => [
                $repositoryOne,
                $repositoryTwo,
            ],
        ]);

        self::assertContains($repositoryOne, $organization->repositories());
        self::assertContains($repositoryTwo, $organization->repositories());
    }

    public function testCreateEstablishesBidirectionalOneToManyAssociations(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Repository::class);

        $organization = $repository->organization();

        self::assertContains($repository, $organization->repositories());
    }

    public function testCreateResolvesReferenceToReferencedEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Project::class, [
            'repository' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Project $project */
        $project = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Project::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Repository::class, $project->repository());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $count
     */
    public function testCreateResolvesReferenceToCollectionOfReferencedEntity(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(
                Fixture\FixtureFactory\Entity\Repository::class,
                $count
            ),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($count, $repositories);
    }

    public function testCreateAllowsOverridingFieldWithDifferentValueWhenFieldDefinitionHasBeenSpecified(): void
    {
        $faker = self::faker()->unique();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        $name = $faker->word;

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        self::assertSame($name, $organization->name());
    }

    public function testCreateAllowsOverridingAssociationWithNullWhenFieldDefinitionHasBeenSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => FieldDefinition::references(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => null,
        ]);

        self::assertNull($repository->template());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $count
     */
    public function testCreateAllowsOverridingAssociationWithCollectionOfEntitiesWhenFieldDefinitionHasBeenSpecified(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => $fixtureFactory->createMultiple(Fixture\FixtureFactory\Entity\Repository::class, [], $count),
        ]);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($count, $repositories);
    }

    public function testDefineEntityAcceptsClosureThatWillBeInvokedAfterEntityCreation(): void
    {
        $name = self::faker()->word;

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(
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
        $organization = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        $expectedName = \sprintf(
            '%s-%s',
            $name,
            $name
        );

        self::assertSame($expectedName, $organization->name());
    }

    public function testCreateCreatesReferencedObjectAutomatically(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Repository::class);

        $organizationOne = $repositoryOne->organization();
        $organizationTwo = $repositoryTwo->organization();

        self::assertNotNull($organizationOne);
        self::assertNotNull($organizationTwo);
        self::assertNotSame($organizationOne, $organizationTwo);
    }

    public function testCreatesCreatesReferencedObjectsTransitively(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Project::class, [
            'repository' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $project = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Project::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Project::class, $project);

        /** @var Fixture\FixtureFactory\Entity\Project $project */
        $repository = $project->repository();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Repository::class, $repository);

        $organization = $repository->organization();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Organization::class, $organization);
    }

    public function testCreateResolvesSequenceToStringValueWhenPercentDPlaceholderIsPresent(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => FieldDefinition::sequence('beta-%d'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationThree */
        $organizationThree = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationFour */
        $organizationFour = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame('beta-1', $organizationOne->name());
        self::assertSame('beta-2', $organizationTwo->name());
        self::assertSame('beta-3', $organizationThree->name());
        self::assertSame('beta-4', $organizationFour->name());
    }

    public function testCreateResolvesSequenceToStringValueWhenPercentDPlaceholderIsNotPresent(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => FieldDefinition::sequence('gamma-'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationThree */
        $organizationThree = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationFour */
        $organizationFour = $fixtureFactory->create(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame('gamma-1', $organizationOne->name());
        self::assertSame('gamma-2', $organizationTwo->name());
        self::assertSame('gamma-3', $organizationThree->name());
        self::assertSame('gamma-4', $organizationFour->name());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intLessThanOne()
     *
     * @param int $count
     */
    public function testCreateMultipleThrowsInvalidCountExceptionWhenCountIsLessThanOne(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $this->expectException(Exception\InvalidCount::class);

        $fixtureFactory->createMultiple(
            Fixture\FixtureFactory\Entity\Organization::class,
            [],
            $count
        );
    }

    public function testCreateMultipleReturnsArrayOfEntitiesWhenCountIsNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $entities = $fixtureFactory->createMultiple(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertCount(1, $entities);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $count
     */
    public function testCreateMultipleReturnsArrayOfEntitiesWhenCountIsSpecified(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $entities = $fixtureFactory->createMultiple(
            Fixture\FixtureFactory\Entity\Organization::class,
            [],
            $count
        );

        self::assertCount($count, $entities);
    }
}
