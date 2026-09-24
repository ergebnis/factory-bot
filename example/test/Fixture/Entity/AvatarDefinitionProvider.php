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

final class AvatarDefinitionProvider implements EntityDefinitionProvider
{
    public function accept(FixtureFactory $fixtureFactory): void
    {
        $fixtureFactory->define(Entity\Avatar::class, [
            'height' => FieldDefinition::closure(static function (Generator $faker): int {
                return $faker->numberBetween(300, 600);
            }),
            'url' => FieldDefinition::closure(static function (Generator $faker): string {
                return $faker->imageUrl();
            }),
            'width' => FieldDefinition::closure(static function (Generator $faker): int {
                return $faker->numberBetween(400, 900);
            }),
        ]);
    }
}
