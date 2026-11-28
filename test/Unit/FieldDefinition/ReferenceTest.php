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

namespace Ergebnis\FactoryBot\Test\Unit\FieldDefinition;

use Ergebnis\FactoryBot;
use Example\Entity;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\FieldDefinition\Reference::class)]
#[Framework\Attributes\UsesClass(FactoryBot\EntityDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FixtureFactory::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\FieldValueResolution\WithOrWithoutOptionalFieldValue::class)]
final class ReferenceTest extends FactoryBot\Test\Unit\AbstractTestCase
{
    public function testResolvesToObjectCreatedByFixtureFactory(): void
    {
        $className = Entity\User::class;

        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define($className);

        $fieldDefinition = new FactoryBot\FieldDefinition\Reference($className);

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

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define($className, [
            'login' => $faker->userName(),
        ]);

        $overriddenLogin = $faker->userName();

        $fieldDefinition = new FactoryBot\FieldDefinition\Reference(
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
