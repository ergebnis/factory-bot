<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

use Doctrine\ORM;
use Ergebnis\FactoryBot\Test\Util;

require_once __DIR__ . '/../vendor/autoload.php';

$entityManager = Util\Doctrine\ORM\EntityManagerFactory::create();

return ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
