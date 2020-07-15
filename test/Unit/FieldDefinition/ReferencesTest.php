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

namespace Ergebnis\FactoryBot\Test\Unit\FieldDefinition;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\FieldDefinition\References;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Unit;
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
 */
final class ReferencesTest extends Unit\AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testResolvesToArrayOfObjectsCreatedByFixtureFactory(int $value): void
    {
        $className = Entity\User::class;
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define($className);

        $fieldDefinition = new References(
            $className,
            Count::exact($value)
        );

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertIsArray($resolved);
        self::assertCount($value, $resolved);
        self::assertContainsOnly($className, $resolved);
    }
}
