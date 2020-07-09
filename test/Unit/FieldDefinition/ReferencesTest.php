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

use Ergebnis\FactoryBot\FieldDefinition\References;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Number;
use Ergebnis\FactoryBot\Test\Fixture;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition\References
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidNumber
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 * @uses \Ergebnis\FactoryBot\Number
 */
final class ReferencesTest extends AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     *
     * @param int $value
     */
    public function testResolvesToArrayOfObjectsCreatedByFixtureFactory(int $value): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define($className);

        $fieldDefinition = new References(
            $className,
            Number::exact($value)
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
