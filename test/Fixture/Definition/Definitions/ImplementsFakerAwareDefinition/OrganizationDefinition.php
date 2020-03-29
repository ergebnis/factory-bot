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

namespace Ergebnis\FactoryBot\Test\Fixture\Definition\Definitions\ImplementsFakerAwareDefinition;

use Ergebnis\FactoryBot\Definition\FakerAwareDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;
use Faker\Generator;

final class OrganizationDefinition implements FakerAwareDefinition
{
    /**
     * @var null|Generator
     */
    private $faker;

    public function accept(FixtureFactory $factory, Generator $faker): void
    {
        $factory->defineEntity(Fixture\FixtureFactory\Entity\Organization::class);
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
