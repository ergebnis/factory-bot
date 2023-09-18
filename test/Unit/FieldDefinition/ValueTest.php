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

use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test;

/**
 * @covers \Ergebnis\FactoryBot\FieldDefinition\Value
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class ValueTest extends Test\Unit\AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::arbitrary()
     */
    public function testResolvesToValue(mixed $value): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $fieldDefinition = new FieldDefinition\Value($value);

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory,
        );

        self::assertSame($value, $resolved);
    }
}
