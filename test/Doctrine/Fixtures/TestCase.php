<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use PHPUnit\Framework;
use FactoryGirl\Tests\Provider\Doctrine\TestDb;
use FactoryGirl\Provider\Doctrine\FixtureFactory;
use Doctrine\ORM\EntityManager;
use Exception;

abstract class TestCase extends Framework\TestCase
{
    /**
     * @var TestDb
     */
    protected $testDb;

    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testDb = new TestDb(
            __DIR__ . '/../../Fixture/Entity',
            __DIR__ . '/TestProxy',
            'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestProxy'
        );

        $this->em = $this->testDb->createEntityManager();
    }
}
