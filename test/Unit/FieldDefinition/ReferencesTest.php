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

#[Framework\Attributes\CoversClass(FactoryBot\FieldDefinition\References::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Count::class)]
#[Framework\Attributes\UsesClass(FactoryBot\EntityDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Exception\InvalidCount::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Value::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FixtureFactory::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\CountResolution\BetweenMinimumAndMaximumCount::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldResolution\FieldValueResolution\WithOrWithoutOptionalFieldValue::class)]
final class ReferencesTest extends FactoryBot\Test\Unit\AbstractTestCase
{
    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testResolvesToArrayOfObjectsCreatedByFixtureFactory(int $value): void
    {
        $className = Entity\User::class;
        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define($className);

        $fieldDefinition = new FactoryBot\FieldDefinition\References(
            $className,
            FactoryBot\Count::exact($value),
        );

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertIsArray($resolved);
        self::assertCount($value, $resolved);
        self::assertContainsOnly($className, $resolved);
    }

    public function testResolvesToArrayOfObjectsCreatedByFixtureFactoryWhenSpecifyingFieldDefinitionOverrides(): void
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

        $fieldDefinition = new FactoryBot\FieldDefinition\References(
            $className,
            FactoryBot\Count::exact(3),
            [
                'login' => $overriddenLogin,
            ],
        );

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertIsArray($resolved);
        self::assertCount(3, $resolved);
        self::assertContainsOnly($className, $resolved);

        $logins = \array_unique(\array_map(static function (Entity\User $user): string {
            return $user->login();
        }, $resolved));

        $expectedLogins = [
            $overriddenLogin,
        ];

        self::assertSame($expectedLogins, $logins);
    }
}
