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

use Ergebnis\FactoryBot\Definitions;
use Ergebnis\FactoryBot\EntityDefinition;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Definitions
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidDirectory
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class DefinitionsTest extends AbstractTestCase
{
    public function testInRejectsNonExistentDirectory(): void
    {
        $this->expectException(Exception\InvalidDirectory::class);

        Definitions::in(__DIR__ . '/../Fixture/Definitions/NonExistentDirectory');
    }

    public function testInThrowsInvalidDefinitionExceptionWhenDefinitionCanNotBeAutoloaded(): void
    {
        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'Definition "%s" can not be autoloaded.',
            Fixture\Definitions\CanNotBeAutoloaded\RepositoryDefinitionButCanNotBeAutoloaded::class
        ));

        Definitions::in(__DIR__ . '/../Fixture/Definitions/CanNotBeAutoloaded');
    }

    public function testInIgnoresClassesWhichDoNotImplementDefinitionInterface(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $definitions = Definitions::in(__DIR__ . '/../Fixture/Definitions/DoesNotImplementDefinition');

        $definitions->registerWith($fixtureFactory);

        $registeredDefinitions = $fixtureFactory->definitions();

        self::assertCount(2, $registeredDefinitions);
        self::assertContainsOnly(EntityDefinition::class, $registeredDefinitions);
        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\Organization::class, $registeredDefinitions);
        self::assertArrayNotHasKey(Fixture\FixtureFactory\Entity\Repository::class, $registeredDefinitions);
        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\User::class, $registeredDefinitions);
    }

    public function testInIgnoresDefinitionsThatAreAbstract(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $definitions = Definitions::in(__DIR__ . '/../Fixture/Definitions/ImplementsDefinitionButIsAbstract');

        $definitions->registerWith($fixtureFactory);

        $registeredDefinitions = $fixtureFactory->definitions();

        self::assertCount(2, $registeredDefinitions);
        self::assertContainsOnly(EntityDefinition::class, $registeredDefinitions);
        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\Organization::class, $registeredDefinitions);
        self::assertArrayNotHasKey(Fixture\FixtureFactory\Entity\Repository::class, $registeredDefinitions);
        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\User::class, $registeredDefinitions);
    }

    public function testInThrowsInvalidDefinitionExceptionWhenDefinitionCanNotBeInstantiated(): void
    {
        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'Definition "%s" can not be instantiated.',
            Fixture\Definitions\ImplementsDefinitionButCanNotBeInstantiated\RepositoryDefinition::class
        ));

        Definitions::in(__DIR__ . '/../Fixture/Definitions/ImplementsDefinitionButCanNotBeInstantiated');
    }

    public function testInThrowsInvalidDefinitionExceptionWhenExceptionIsThrownDuringInstantiationOfDefinition(): void
    {
        $this->expectException(Exception\InvalidDefinition::class);
        $this->expectExceptionMessage(\sprintf(
            'An exception was thrown while trying to instantiate definition "%s".',
            Fixture\Definitions\ImplementsDefinitionButThrowsExceptionDuringConstruction\RepositoryDefinition::class
        ));

        Definitions::in(__DIR__ . '/../Fixture/Definitions/ImplementsDefinitionButThrowsExceptionDuringConstruction');
    }

    public function testInAcceptsDefinitionsThatHaveNoIssues(): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $definitions = Definitions::in(__DIR__ . '/../Fixture/Definitions/ImplementsDefinition');

        $definitions->registerWith($fixtureFactory);

        $registeredDefinitions = $fixtureFactory->definitions();

        self::assertCount(3, $registeredDefinitions);
        self::assertContainsOnly(EntityDefinition::class, $registeredDefinitions);
        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\Organization::class, $registeredDefinitions);
        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\Repository::class, $registeredDefinitions);
        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\User::class, $registeredDefinitions);
    }
}
