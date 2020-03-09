<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework;
use FactoryGirl\Provider\Doctrine\FixtureFactory;
use Doctrine\ORM\EntityManager;
use Exception;

abstract class TestCase extends Framework\TestCase
{
    final protected static function createEntityManager(): EntityManager
    {
        $annotationPath = __DIR__ . '/../../Fixture/Entity';
        $proxyDir = __DIR__ . '/../Fixtures/TestProxy';
        $proxyNamespace = 'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestProxy';

        $cache = new ArrayCache();

        $config = new Configuration();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setMetadataDriverImpl(
            $config->newDefaultAnnotationDriver($annotationPath)
        );
        $config->setProxyDir($proxyDir);
        $config->setProxyNamespace($proxyNamespace);
        $config->setAutoGenerateProxyClasses(true);

        $em = EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'path' => ':memory:'
            ],
            $config
        );

        $tool = new SchemaTool($em);

        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        return $em;
    }
}
