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

namespace Ergebnis\FactoryBot\Test\AutoReview;

use Ergebnis\Test\Util\Helper;
use FactoryGirl\Provider\Doctrine;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @coversNothing
 */
final class SrcCodeTest extends Framework\TestCase
{
    use Helper;

    public function testSrcClassesHaveUnitTests(): void
    {
        self::assertClassesHaveTests(
            __DIR__ . '/../../src/',
            'Ergebnis\\FactoryBot\\',
            'Ergebnis\\FactoryBot\\Test\\Unit',
            [
                Doctrine\EntityDef::class,
                Doctrine\EntityDefinitionUnavailable::class,
                Doctrine\FieldDef::class,
                Doctrine\FixtureFactory::class,
                Doctrine\ORM\Locking\LockException::class,
                Doctrine\ORM\Locking\TableLock::class,
                Doctrine\ORM\Locking\TableLockMode::class,
                Doctrine\ORM\QueryBuilder::class,
                Doctrine\ORM\Repository::class,
            ]
        );
    }
}
