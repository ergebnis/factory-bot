<?php

namespace FactoryGirl\Tests\Provider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;

/**
 * @category FactoryGirl
 * @package  Doctrine
 * @author   Martin Pärtel
 * @author   Mikko Hirvonen <mikko.petteri.hirvonen@gmail.com>
 * @license  http://www.opensource.org/licenses/BSD-3-Clause New BSD License
 */
class TestDb
{
    /**
     * @var \Doctrine\ORM\Configuration
     */
    private $doctrineConfig;

    /**
     * @return EntityManager
     */
    public function createEntityManager()
    {
        $annotationPath = __DIR__ . '/../Fixture/Entity';
        $proxyDir = __DIR__ . '/Fixtures/TestProxy';
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

        $this->doctrineConfig = $config;

        $em = EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'path'   => ':memory:'
            ],
            $this->doctrineConfig
        );

        $tool = new SchemaTool($em);

        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        return $em;
    }
}
