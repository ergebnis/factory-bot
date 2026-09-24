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

final class UserDefinitionProvider implements EntityDefinitionProvider
{
    public function accept(FixtureFactory $fixtureFactory): void
    {
        $fixtureFactory->define(Entity\User::class, [
            'avatar' => FieldDefinition::reference(Entity\Avatar::class),
            'id' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->uuid();
            }),
            'location' => FieldDefinition::optionalClosure(static function (Generator $faker): string {
                return $faker->city();
            }),
            'login' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->userName();
            }),
        ]);
    }
}
