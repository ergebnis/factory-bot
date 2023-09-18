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

use Ergebnis\FactoryBot\EntityDefinition;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Strategy;
use Ergebnis\FactoryBot\Test;
use Example\Entity;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FieldDefinition\Reference::class)]
#[Framework\Attributes\UsesClass(EntityDefinition::class)]
#[Framework\Attributes\UsesClass(FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FixtureFactory::class)]
#[Framework\Attributes\UsesClass(Strategy\DefaultStrategy::class)]
final class ReferenceTest extends Test\Unit\AbstractTestCase
{
    public function testResolvesToObjectCreatedByFixtureFactory(): void
    {
        $className = Entity\User::class;

        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define($className);

        $fieldDefinition = new FieldDefinition\Reference($className);

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertInstanceOf($className, $resolved);
    }

    public function testResolvesToObjectCreatedByFixtureFactoryWhenSpecifyingFieldDefinitionOverrides(): void
    {
        $className = Entity\User::class;

        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define($className, [
            'login' => $faker->userName(),
        ]);

        $overriddenLogin = $faker->userName();

        $fieldDefinition = new FieldDefinition\Reference(
            $className,
            [
                'login' => $overriddenLogin,
            ],
        );

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertInstanceOf($className, $resolved);
        self::assertSame($overriddenLogin, $resolved->login());
    }
}
