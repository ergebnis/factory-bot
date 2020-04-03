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

namespace Ergebnis\FactoryBot\Test\Unit\Definition;

use Ergebnis\FactoryBot\Definition\Definitions;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Definition\Definitions
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

        Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/NonExistentDirectory');
    }

    public function testInIgnoresClassesWhichDoNotImplementProviderInterface(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/DoesNotImplementDefinition');

        $definitions->registerWith(
            $fixtureFactory,
            $faker
        );

        self::assertSame([], $fixtureFactory->definitions());
    }

    public function testInIgnoresDefinitionsThatAreAbstract(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/ImplementsDefinitionButIsAbstract');

        $definitions->registerWith(
            $fixtureFactory,
            $faker
        );

        self::assertSame([], $fixtureFactory->definitions());
    }

    public function testInIgnoresDefinitionsThatHavePrivateConstructors(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/ImplementsDefinitionButHasPrivateConstructor');

        $definitions->registerWith(
            $fixtureFactory,
            $faker
        );

        self::assertSame([], $fixtureFactory->definitions());
    }

    public function testInThrowsInvalidDefinitionExceptionWhenExceptionIsThrownDuringInstantiationOfDefinition(): void
    {
        $this->expectException(Exception\InvalidDefinition::class);

        Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/ImplementsDefinitionButThrowsExceptionDuringConstruction');
    }

    public function testInAcceptsDefinitionsThatHaveNoIssues(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/ImplementsDefinition');

        $definitions->registerWith(
            $fixtureFactory,
            $faker
        );

        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\Repository::class, $fixtureFactory->definitions());
    }
}
