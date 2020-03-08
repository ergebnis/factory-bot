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

namespace Ergebnis\FactoryGirl\Definition\Test\Fixture\Definition\FakerAware;

use Ergebnis\FactoryGirl\Definition\FakerAwareDefinition;
use Ergebnis\FactoryGirl\Definition\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FixtureFactory;
use Faker\Generator;

final class GroupDefinition implements FakerAwareDefinition
{
    /**
     * @var null|Generator
     */
    private $faker;

    public function accept(FixtureFactory $factory): void
    {
        $factory->defineEntity(Entity\Group::class);
    }

    public function provideWith(Generator $faker): void
    {
        $this->faker = $faker;
    }

    public function faker(): Generator
    {
        if (null === $this->faker) {
            throw new \RuntimeException(\sprintf(
                'An instance of "%s" has not been provided yet.',
                Generator::class
            ));
        }

        return $this->faker;
    }
}
