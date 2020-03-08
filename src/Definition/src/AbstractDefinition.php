<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-girl-definition
 */

namespace Ergebnis\FactoryGirl\Definition;

use Faker\Generator;

abstract class AbstractDefinition implements FakerAwareDefinition
{
    /**
     * @var null|Generator
     */
    private $faker;

    final public function provideWith(Generator $faker): void
    {
        $this->faker = $faker;
    }

    /**
     * @throws \BadMethodCallException
     *
     * @return Generator
     */
    final public function faker(): Generator
    {
        if (null === $this->faker) {
            throw new \BadMethodCallException(\sprintf(
                'Before accessing, an instance of "%s" needs to be provided using provideWith()',
                Generator::class
            ));
        }

        return $this->faker;
    }
}
