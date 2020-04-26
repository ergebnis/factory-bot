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

use Ergebnis\FactoryBot\FieldDefinition\Value;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition\Value
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class ValueTest extends AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::arbitrary()
     *
     * @param mixed $value
     */
    public function testOptionalResolvesToValue($value): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fieldDefinition = Value::required($value);

        self::assertTrue($fieldDefinition->isRequired());

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertSame($value, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::arbitrary()
     *
     * @param mixed $value
     */
    public function testRequiredResolvesToValue($value): void
    {
        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fieldDefinition = Value::required($value);

        self::assertTrue($fieldDefinition->isRequired());

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertSame($value, $resolved);
    }
}
