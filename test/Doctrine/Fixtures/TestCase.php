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
     * @var EntityManager
     */
    protected $em;

    protected function setUp(): void
    {
        parent::setUp();

        $testDb = new TestDb();

        $this->em = $testDb->createEntityManager();
    }
}
