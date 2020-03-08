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

namespace Ergebnis\FactoryBot\Definition;

use FactoryGirl\Provider\Doctrine\FixtureFactory;

interface Definition
{
    public function accept(FixtureFactory $fixtureFactory): void;
}
