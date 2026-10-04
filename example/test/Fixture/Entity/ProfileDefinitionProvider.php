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

namespace Example\Test\Fixture\Entity;

use Ergebnis\FactoryBot\EntityDefinitionProvider;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Example\Entity;
use Faker\Generator;

final class ProfileDefinitionProvider implements EntityDefinitionProvider
{
    public function accept(FixtureFactory $fixtureFactory): void
    {
        $fixtureFactory->define(Entity\Profile::class, [
            'bio' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->sentence();
            }),
            'id' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->uuid();
            }),
            'user' => FieldDefinition::reference(Entity\User::class),
        ]);
    }
}
