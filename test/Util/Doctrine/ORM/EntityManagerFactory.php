<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Util\Doctrine\ORM;

use Doctrine\DBAL;
use Doctrine\ORM;

final class EntityManagerFactory
{
    public static function create(): ORM\EntityManagerInterface
    {
        $connection = DBAL\DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => ':memory:',
        ]);

        $configuration = ORM\ORMSetup::createConfiguration(true);

        $configuration->setMetadataDriverImpl(new ORM\Mapping\Driver\AttributeDriver([
            __DIR__ . '/../../../../example/src/Entity',
        ]));

        return new ORM\EntityManager(
            $connection,
            $configuration,
        );
    }
}
