<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

use Ergebnis\FactoryBot;

require_once __DIR__ . '/../vendor/autoload.php';

return FactoryBot\Test\Util\Doctrine\ORM\EntityManagerFactory::create();
