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

namespace Ergebnis\FactoryBot\Test\Unit;

use Doctrine\ORM;
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

abstract class AbstractTestCase extends Framework\TestCase
{
    use Test\Util\Helper;

    final protected static function entityManager(): ORM\EntityManagerInterface
    {
        return Test\Util\Doctrine\ORM\EntityManagerFactory::create();
    }
}
