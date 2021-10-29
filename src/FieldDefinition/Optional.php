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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

/**
 * @internal
 */
final class Optional implements Resolvable
{
    /**
     * @var Resolvable
     */
    private $resolvable;

    public function __construct(Resolvable $resolvable)
    {
        $this->resolvable = $resolvable;
    }

    /**
     * @return mixed
     */
    public function resolve(
        Generator $faker,
        FixtureFactory $fixtureFactory
    ) {
        return $this->resolvable->resolve(
            $faker,
            $fixtureFactory,
        );
    }
}
