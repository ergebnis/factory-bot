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

#[\PHPUnit\Framework\Attributes\CoversClass(\Ergebnis\FactoryBot\FieldDefinition\Value::class)]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\EntityDefinition')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FixtureFactory')]
final class ValueTest extends Test\Unit\AbstractTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::class, 'arbitrary')]
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
