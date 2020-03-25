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

use Ergebnis\FactoryBot\Definition\Definition;
use Ergebnis\FactoryBot\Definition\Definitions;
use Ergebnis\FactoryBot\Definition\FakerAwareDefinition;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;
use Ergebnis\Test\Util\Helper;
use Faker\Generator;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Definition\Definitions
 *
 * @uses \Ergebnis\FactoryBot\EntityDef
 * @uses \Ergebnis\FactoryBot\Exception\InvalidDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidDirectory
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class DefinitionsTest extends AbstractTestCase
{
    use Helper;

    public function testInRejectsNonExistentDirectory(): void
    {
        $this->expectException(Exception\InvalidDirectory::class);

        Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/NonExistentDirectory');
    }

    public function testInIgnoresClassesWhichDoNotImplementProviderInterface(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/DoesNotImplementInterface');

        $definitions->registerWith($fixtureFactory);

        self::assertSame([], $fixtureFactory->definitions());
    }

    public function testInIgnoresClassesWhichAreAbstract(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/IsAbstract');

        $definitions->registerWith($fixtureFactory);

        self::assertSame([], $fixtureFactory->definitions());
    }

    public function testInIgnoresClassesWhichHavePrivateConstructors(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/PrivateConstructor');

        $definitions->registerWith($fixtureFactory);

        self::assertSame([], $fixtureFactory->definitions());
    }

    public function testInAcceptsClassesWhichAreAcceptable(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/Acceptable');

        $definitions->registerWith($fixtureFactory);

        self::assertArrayHasKey(Fixture\FixtureFactory\Entity\Repository::class, $fixtureFactory->definitions());
    }

    public function testFluentInterface(): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/Acceptable');

        self::assertSame($definitions, $definitions->registerWith($fixtureFactory));
        self::assertSame($definitions, $definitions->provideWith($this->prophesize(Generator::class)->reveal()));
    }

    public function testInAcceptsClassesWhichAreAcceptableAndFakerAwareAndProvidesThemWithFaker(): void
    {
        $faker = $this->prophesize(Generator::class);

        $definitions = Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/FakerAware');

        $definitions->provideWith($faker->reveal());

        $reflection = new \ReflectionClass(Definitions::class);

        $property = $reflection->getProperty('definitions');

        $property->setAccessible(true);

        $foundDefinitions = $property->getValue($definitions);

        self::assertIsArray($foundDefinitions);

        $fakerAwareDefinitions = \array_filter($foundDefinitions, static function (Definition $definition): bool {
            return $definition instanceof FakerAwareDefinition;
        });

        self::assertCount(1, $fakerAwareDefinitions);
        self::assertContainsOnlyInstancesOf(FakerAwareDefinition::class, $fakerAwareDefinitions);

        $fakerAwareDefinition = \array_shift($fakerAwareDefinitions);

        self::assertInstanceOf(Fixture\Definition\Definitions\FakerAware\OrganizationDefinition::class, $fakerAwareDefinition);
        self::assertSame($faker->reveal(), $fakerAwareDefinition->faker());
    }

    public function testThrowsInvalidDefinitionExceptionIfInstantiatingDefinitionsThrowsException(): void
    {
        $this->expectException(Exception\InvalidDefinition::class);

        Definitions::in(__DIR__ . '/../../Fixture/Definition/Definitions/ThrowsExceptionDuringConstruction');
    }
}
