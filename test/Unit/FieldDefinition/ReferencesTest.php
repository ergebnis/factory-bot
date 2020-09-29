<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit\FieldDefinition;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test;
use Example\Entity;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition\References
 *
 * @uses \Ergebnis\FactoryBot\Count
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 * @uses \Ergebnis\FactoryBot\Strategy\DefaultStrategy
 */
final class ReferencesTest extends Test\Unit\AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testResolvesToArrayOfObjectsCreatedByFixtureFactory(int $value): void
    {
        $className = Entity\User::class;
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define($className);

        $fieldDefinition = new FieldDefinition\References(
            $className,
            Count::exact($value),
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

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fixtureFactory->define($className, [
            'login' => $faker->userName,
        ]);

        $overridenLogin = $faker->userName;

        $fieldDefinition = new FieldDefinition\References(
            $className,
            Count::exact(3),
            [
                'login' => $overridenLogin,
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
            $overridenLogin,
        ];

        self::assertSame($expectedLogins, $logins);
    }
}
