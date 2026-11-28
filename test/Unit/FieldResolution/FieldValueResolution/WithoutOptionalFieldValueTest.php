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

namespace Ergebnis\FactoryBot\Test\Unit\FieldResolution\FieldValueResolution;

use Ergebnis\FactoryBot;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\FieldResolution\FieldValueResolution\WithoutOptionalFieldValue::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Count::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Optional::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FixtureFactory::class)]
final class WithoutOptionalFieldValueTest extends FactoryBot\Test\Unit\AbstractTestCase
{
    public function testResolveFieldValueResolvesOptionalFieldDefinitionToNullWhenFakerReturnsTrue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FactoryBot\FieldDefinition::optionalValue($faker->sentence());

        $strategy = new FactoryBot\FieldResolution\FieldValueResolution\WithoutOptionalFieldValue();

        $resolved = $strategy->resolveFieldValue(
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        self::assertNull($resolved);
    }

    public function testResolveFieldValueResolvesOptionalFieldDefinitionToNullWhenFakerReturnsFalse(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FactoryBot\FieldDefinition::optionalValue($faker->sentence());

        $strategy = new FactoryBot\FieldResolution\FieldValueResolution\WithoutOptionalFieldValue();

        $resolved = $strategy->resolveFieldValue(
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        self::assertNull($resolved);
    }

    public function testResolveFieldValueResolvesFieldDefinitionWithFakerAndFixtureFactoryWhenFakerReturnsTrue(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FactoryBot\FieldDefinition::value($faker->sentence());

        $strategy = new FactoryBot\FieldResolution\FieldValueResolution\WithoutOptionalFieldValue();

        $resolved = $strategy->resolveFieldValue(
            new FactoryBot\Test\Double\Faker\TrueGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertSame($expected, $resolved);
    }

    public function testResolveFieldValueResolvesFieldDefinitionWithoutFakerAndFixtureFactoryWhenFakerReturnsFalse(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = FactoryBot\FieldDefinition::value($faker->sentence());

        $strategy = new FactoryBot\FieldResolution\FieldValueResolution\WithoutOptionalFieldValue();

        $resolved = $strategy->resolveFieldValue(
            new FactoryBot\Test\Double\Faker\FalseGenerator(),
            $fixtureFactory,
            $fieldDefinition,
        );

        $expected = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertSame($expected, $resolved);
    }
}
