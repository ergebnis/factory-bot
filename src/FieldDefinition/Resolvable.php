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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\FixtureFactory;

interface Resolvable
{
    /**
     * @param FixtureFactory $fixtureFactory
     *
     * @return mixed
     */
    public function resolve(FixtureFactory $fixtureFactory);
}
