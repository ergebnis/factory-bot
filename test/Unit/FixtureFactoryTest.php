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
use Ergebnis\Test\Util\Helper;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition
 * @covers \Ergebnis\FactoryBot\FixtureFactory
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\EntityDefinitionAlreadyRegistered
 * @uses \Ergebnis\FactoryBot\Exception\EntityDefinitionUnavailable
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\Exception\InvalidFieldNames
 */
final class FixtureFactoryTest extends AbstractTestCase
{
    use Helper;

    public function testDefineEntityThrowsExceptionWhenDefinitionHasAlreadyBeenProvidedForEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $this->expectException(Exception\EntityDefinitionAlreadyRegistered::class);
        $this->expectExceptionMessage(\sprintf(
            'An entity definition for class name "%s" has already been registered.',
            Fixture\FixtureFactory\Entity\Organization::class
        ));

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);
    }

    public function testDefineEntityThrowsExceptionWhenClassDoesNotExist(): void
    {
        $className = 'NotAClass';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Not a class: %s',
            $className
        ));

        $fixtureFactory->defineEntity($className);
    }

    public function testDefineEntityThrowsExceptionWhenClassNameDoesNotReferenceAnEntity(): void
    {
        $className = Fixture\FixtureFactory\NotAnEntity\User::class;

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Class "%s" is not a valid entity or mapped super class.',
            $className
        ));

        $fixtureFactory->defineEntity($className);
    }

    public function testDefineEntityThrowsInvalidFieldNamesExceptionWhenUsingFieldNamesThatDoNotExistInEntity(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(Exception\InvalidFieldNames::class);
        $this->expectExceptionMessage(\sprintf(
            'Entity "%s" does not have fields with the names "email", "phone".',
            Fixture\FixtureFactory\Entity\User::class
        ));

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class, [
            'avatar' => new Fixture\FixtureFactory\Entity\Avatar(),
            'email' => $faker->email,
            'phone' => $faker->phoneNumber,
        ]);
    }

    public function testDefineEntityReturnsFixtureFactory(): void
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        self::assertSame($fixtureFactory, $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class));
    }

    public function testGetThrowsEntityDefinitionUnavailableWhenDefinitionIsUnavailable(): void
    {
        $className = self::class;

        $entityManager = $this->prophesize(ORM\EntityManagerInterface::class)->reveal();

        $fixtureFactory = new FixtureFactory($entityManager);

        $this->expectException(Exception\EntityDefinitionUnavailable::class);
        $this->expectExceptionMessage(\sprintf(
            'An entity definition for class name "%s" is not available.',
            $className
        ));

        $fixtureFactory->get($className);
    }

    public function testGetThrowsInvalidFieldNamesExceptionWhenReferencingFieldsThatDoNotExistInEntity(): void
    {
        $faker = self::faker()->unique();

        $fieldName = $faker->word;

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        $this->expectException(Exception\InvalidFieldNames::class);
        $this->expectExceptionMessage(\sprintf(
            'Entity "%s" does not have a field with the name "%s".',
            Fixture\FixtureFactory\Entity\Organization::class,
            $fieldName
        ));

        $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class, [
            $fieldName => 'blueberry',
        ]);
    }

    public function testDefineEntityAllowsDefiningAndReferencingEmbeddables(): void
    {
        $url = self::faker()->imageUrl();

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Avatar::class, [
            'url' => $url,
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class, [
            'avatar' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Avatar::class),
        ]);

        $user = $fixtureFactory->get(Fixture\FixtureFactory\Entity\User::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\User::class, $user);

        /** @var Fixture\FixtureFactory\Entity\User $user */
        $avatar = $user->avatar();

        self::assertSame($url, $avatar->url());
    }

    public function testDefineEntityThrowsInvalidFieldNamesExceptionWhenReferencingFieldsOfEmbeddablesUsingDotNotation(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(Exception\InvalidFieldNames::class);
        $this->expectExceptionMessage(\sprintf(
            'Entity "%s" does not have fields with the names "avatar.height", "avatar.url", "avatar.width".',
            Fixture\FixtureFactory\Entity\User::class
        ));

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class, [
            'login' => $faker->userName,
            'avatar.url' => $faker->imageUrl(),
            'avatar.width' => $faker->numberBetween(100, 250),
            'avatar.height' => $faker->numberBetween(100, 250),
        ]);
    }

    public function testGetThrowsInvalidFieldNamesExceptionWhenReferencingFieldsOfEmbeddablesUsingDotNotation(): void
    {
        $faker = self::faker()->unique();

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $this->expectException(Exception\InvalidFieldNames::class);
        $this->expectExceptionMessage(\sprintf(
            'Entity "%s" does not have fields with the names "avatar.height", "avatar.url", "avatar.width".',
            Fixture\FixtureFactory\Entity\User::class
        ));

        $fixtureFactory->get(Fixture\FixtureFactory\Entity\User::class, [
            'login' => $faker->userName,
            'avatar.url' => $faker->imageUrl(),
            'avatar.width' => $faker->numberBetween(100, 250),
            'avatar.height' => $faker->numberBetween(100, 250),
        ]);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intLessThanOne()
     *
     * @param int $count
     */
    public function testGetListThrowsInvalidCountExceptionWhenCountIsLessThanOne(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $this->expectException(Exception\InvalidCount::class);
        $this->expectExceptionMessage(\sprintf(
            'Count needs to be greater than or equal to 1, but %d is not.',
            $count
        ));

        $fixtureFactory->getList(
            Fixture\FixtureFactory\Entity\Organization::class,
            [],
            $count
        );
    }

    public function testAcceptsConstantValuesInEntityDefinitions(): void
    {
        $name = self::faker()->word;

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame($name, $organization->name());
    }

    public function testAcceptsGeneratorFunctionsInEntityDefinitions(): void
    {
        $name = 'foo';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => static function () use (&$name): string {
                return \sprintf(
                    'the-%s-organization',
                    $name
                );
            },
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        $name = 'bar';

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame('the-foo-organization', $organizationOne->name());
        self::assertSame('the-bar-organization', $organizationTwo->name());
    }

    public function testValuesCanBeOverriddenAtCreationTime(): void
    {
        $faker = self::faker()->unique();

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        $name = $faker->word;

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        self::assertSame($name, $organization->name());
    }

    public function testPreservesDefaultValuesOfEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertFalse($organization->isVerified());
    }

    public function testDoesNotCallTheConstructorOfTheEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, []);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertFalse($organization->constructorWasCalled());
    }

    public function testInstantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertEmpty($organization->repositories());
    }

    public function testArrayElementsAreMappedToCollectionAsscociationFields(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => self::faker()->word,
            'repositories' => [
                $repositoryOne,
                $repositoryTwo,
            ],
        ]);

        self::assertContains($repositoryOne, $organization->repositories());
        self::assertContains($repositoryTwo, $organization->repositories());
    }

    public function testUnspecifiedFieldsAreLeftNull(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertNull($organization->name());
    }

    public function testReturnsListOfEntities(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertCount(1, $fixtureFactory->getList(Fixture\FixtureFactory\Entity\Organization::class));
    }

    public function testCanSpecifyNumberOfReturnedInstances(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertCount(5, $fixtureFactory->getList(Fixture\FixtureFactory\Entity\Organization::class, [], 5));
    }

    public function testBidirectionalOntToManyReferencesAreAssignedBothWays(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Repository::class);

        $organization = $repository->organization();

        self::assertContains($repository, $organization->repositories());
    }

    public function testUnidirectionalReferencesWorkAsUsual(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Project::class, [
            'repository' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Project $project */
        $project = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Project::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Repository::class, $project->repository());
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intBetweenOneAndFive()
     *
     * @param int $count
     */
    public function testReferencedObjectsShouldBeCreatedAutomatically(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

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
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($count, $repositories);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intBetweenOneAndFive()
     *
     * @param int $count
     */
    public function testReferencedObjectsShouldBeOverrideable(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => $fixtureFactory->getList(Fixture\FixtureFactory\Entity\Repository::class, [], $count),
        ]);

        $repositories = $organization->repositories();

        self::assertContainsOnly(Fixture\FixtureFactory\Entity\Repository::class, $repositories);
        self::assertCount($count, $repositories);
    }

    public function testReferencedObjectsShouldBeNullable(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => FieldDefinition::references(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Repository::class, [
            'template' => null,
        ]);

        self::assertNull($repository->template());
    }

    public function testAfterGettingAnEntityAsASingletonGettingTheEntityAgainReturnsTheSameObject(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame($organization, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class));
        self::assertSame($organization, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class));
    }

    public function testGetAsSingletonMethodAcceptsFieldOverridesLikeGet(): void
    {
        $faker = self::faker()->unique();

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $faker->word,
        ]);

        $name = $faker->word;

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => $name,
        ]);

        self::assertSame($name, $organizationOne->name());

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame($name, $organizationTwo->name());
    }

    public function testThrowsAnErrorWhenCallingGetSingletonTwiceOnTheSameEntity(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => self::faker()->word,
        ]);

        $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Organization::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Already a singleton: %s',
            Fixture\FixtureFactory\Entity\Organization::class
        ));

        $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Organization::class);
    }

    //TODO: should it be an error to get() a singleton with overrides?

    public function testAllowsSettingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $organization = new Fixture\FixtureFactory\Entity\Organization(self::faker()->word);

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Organization::class, $organization);

        self::assertSame($organization, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class));
    }

    public function testAllowsUnsettingSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $organization = new Fixture\FixtureFactory\Entity\Organization(self::faker()->word);

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Organization::class, $organization);
        $fixtureFactory->unsetSingleton(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertNotSame($organization, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class));
    }

    public function testAllowsOverwritingExistingSingletons(): void
    {
        $faker = self::faker()->unique();

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $organizationOne = new Fixture\FixtureFactory\Entity\Organization($faker->word);
        $organizationTwo = new Fixture\FixtureFactory\Entity\Organization($faker->word);

        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Organization::class, $organizationOne);
        $fixtureFactory->setSingleton(Fixture\FixtureFactory\Entity\Organization::class, $organizationTwo);

        self::assertSame($organizationTwo, $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class));
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intBetweenOneAndFive()
     *
     * @param int $count
     */
    public function testReferencedObjectsCanBeSingletons(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'repositories' => FieldDefinition::references(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repository */
        $repository = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        $repositories = $organization->repositories();

        self::assertContains($repository, $repositories);
        self::assertCount(1, $repositories);
    }

    public function testWhenTheOneSideIsASingletonItMayGetSeveralChildObjects(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organization */
        $organization = $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Repository::class);

        self::assertContains($repositoryOne, $organization->repositories());
        self::assertContains($repositoryTwo, $organization->repositories());
    }

    public function testDefineEntityAcceptsClosureThatWillBeInvokedAfterEntityCreation(): void
    {
        $name = self::faker()->word;

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

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
        $organization = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        $expectedName = \sprintf(
            '%s-%s',
            $name,
            $name
        );

        self::assertSame($expectedName, $organization->name());
    }

    public function testReferencedObjectShouldBeCreatedAutomatically(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'name' => self::faker()->word,
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryOne */
        $repositoryOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Repository::class);

        /** @var Fixture\FixtureFactory\Entity\Repository $repositoryTwo */
        $repositoryTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Repository::class);

        $organizationOne = $repositoryOne->organization();
        $organizationTwo = $repositoryTwo->organization();

        self::assertNotNull($organizationOne);
        self::assertNotNull($organizationTwo);
        self::assertNotSame($organizationOne, $organizationTwo);
    }

    public function testReferencesGetInstantiatedTransitively(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Project::class, [
            'repository' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $project = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Project::class);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Project::class, $project);

        /** @var Fixture\FixtureFactory\Entity\Project $project */
        $repository = $project->repository();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Repository::class, $repository);

        $organization = $repository->organization();

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\Organization::class, $organization);
    }

    public function testTransitiveReferencesWorkWithSingletons(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Repository::class, [
            'organization' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Organization::class),
        ]);

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Project::class, [
            'repository' => FieldDefinition::reference(Fixture\FixtureFactory\Entity\Repository::class),
        ]);

        $fixtureFactory->getAsSingleton(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Project $projectOne */
        $projectOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Project::class);

        /** @var Fixture\FixtureFactory\Entity\Project $projectTwo */
        $projectTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Project::class);

        $repositoryOne = $projectOne->repository();
        $repositoryTwo = $projectTwo->repository();

        self::assertNotSame($repositoryOne, $repositoryTwo);

        $organizationOne = $repositoryOne->organization();
        $organizationTwo = $repositoryTwo->organization();

        self::assertSame($organizationOne, $organizationTwo);
    }

    public function testSequenceGeneratorCallsAFunctionWithAnIncrementingArgument(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => FieldDefinition::sequence(static function (int $i): string {
                return \sprintf(
                    'alpha-%d',
                    $i
                );
            }),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationThree */
        $organizationThree = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationFour */
        $organizationFour = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame('alpha-1', $organizationOne->name());
        self::assertSame('alpha-2', $organizationTwo->name());
        self::assertSame('alpha-3', $organizationThree->name());
        self::assertSame('alpha-4', $organizationFour->name());
    }

    public function testSequenceGeneratorCanTakeAPlaceholderString(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => FieldDefinition::sequence('beta-%d'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationThree */
        $organizationThree = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationFour */
        $organizationFour = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame('beta-1', $organizationOne->name());
        self::assertSame('beta-2', $organizationTwo->name());
        self::assertSame('beta-3', $organizationThree->name());
        self::assertSame('beta-4', $organizationFour->name());
    }

    public function testSequenceGeneratorCanTakeAStringToAppendTo(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class, [
            'name' => FieldDefinition::sequence('gamma-'),
        ]);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationOne */
        $organizationOne = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationTwo */
        $organizationTwo = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationThree */
        $organizationThree = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        /** @var Fixture\FixtureFactory\Entity\Organization $organizationFour */
        $organizationFour = $fixtureFactory->get(Fixture\FixtureFactory\Entity\Organization::class);

        self::assertSame('gamma-1', $organizationOne->name());
        self::assertSame('gamma-2', $organizationTwo->name());
        self::assertSame('gamma-3', $organizationThree->name());
        self::assertSame('gamma-4', $organizationFour->name());
    }
}
