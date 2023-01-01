<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Integration;

use Doctrine\ORM;
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

/**
 * @internal
 */
abstract class AbstractTestCase extends Framework\TestCase
{
    use Test\Util\Helper;

    final protected static function entityManager(): ORM\EntityManagerInterface
    {
        $entityManager = Test\Util\Doctrine\ORM\EntityManagerFactory::create();

        $schemaTool = new ORM\Tools\SchemaTool($entityManager);

        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        return $entityManager;
    }
}
