<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit\FieldDefinition;

use Ergebnis\FactoryBot\FieldDefinition\Closure;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Unit;
use Example\Entity;
use Faker\Generator;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FieldDefinition\Closure::class)]
#[Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\EntityDefinition')]
#[Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition')]
#[Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\Value')]
#[Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FixtureFactory')]
#[Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\Strategy\DefaultStrategy')]
final class ClosureTest extends Unit\AbstractTestCase
{
    public function testResolvesToResultOfInvokingClosureWithFakerAndFixtureFactory(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define(Entity\User::class);

        $closure = static function (Generator $faker, FixtureFactory $fixtureFactory): Entity\User {
            return $fixtureFactory->createOne(Entity\User::class);
        };

        $fieldDefinition = new Closure($closure);

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertInstanceOf(Entity\User::class, $resolved);
    }
}
