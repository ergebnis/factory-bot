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

namespace Example\Test\Unit;

use Doctrine\ORM;
use Ergebnis\FactoryBot;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework;

/**
 * @internal
 */
abstract class AbstractTestCase extends Framework\TestCase
{
    final protected static function entityManager(): ORM\EntityManagerInterface
    {
        $configuration = ORM\Tools\Setup::createAnnotationMetadataConfiguration(
            [
                __DIR__ . '/../../src/Entity',
            ],
            true,
            null,
            null,
            false,
        );

        $entityManager = ORM\EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'path' => ':memory:',
            ],
            $configuration,
        );

        $schemaTool = new ORM\Tools\SchemaTool($entityManager);

        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        return $entityManager;
    }

    final protected static function faker(): Generator
    {
        $faker = Factory::create();

        $faker->seed(9001);

        return $faker;
    }

    final protected static function fixtureFactory(): FactoryBot\FixtureFactory
    {
        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            self::faker(),
        );

        $fixtureFactory->load(__DIR__ . '/../Fixture');

        return $fixtureFactory;
    }
}
