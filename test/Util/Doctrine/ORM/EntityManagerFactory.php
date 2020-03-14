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

namespace Ergebnis\FactoryBot\Test\Util\Doctrine\ORM;

use Doctrine\ORM;

final class EntityManagerFactory
{
    public static function create(): ORM\EntityManagerInterface
    {
        $configuration = ORM\Tools\Setup::createAnnotationMetadataConfiguration(
            [
                __DIR__ . '/../../../Fixture/FixtureFactory/Entity',
            ],
            true,
            null,
            null,
            false
        );

        return ORM\EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'path' => ':memory:',
            ],
            $configuration
        );
    }
}
