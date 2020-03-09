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

use Doctrine\Common;
use Doctrine\ORM;
use PHPUnit\Framework;

/**
 * @internal
 */
abstract class AbstractTestCase extends Framework\TestCase
{
    final protected static function createEntityManager(): ORM\EntityManager
    {
        $annotationPath = __DIR__ . '/../Fixture/Entity';
        $proxyDir = __DIR__ . '/../Doctrine/Fixtures/TestProxy';
        $proxyNamespace = 'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestProxy';

        $cache = new Common\Cache\ArrayCache();

        $config = new ORM\Configuration();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setMetadataDriverImpl(
            $config->newDefaultAnnotationDriver($annotationPath)
        );
        $config->setProxyDir($proxyDir);
        $config->setProxyNamespace($proxyNamespace);
        $config->setAutoGenerateProxyClasses(true);

        return ORM\EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'path' => ':memory:',
            ],
            $config
        );
    }
}
