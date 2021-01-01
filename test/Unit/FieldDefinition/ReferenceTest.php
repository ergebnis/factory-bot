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

use Ergebnis\FactoryBot\FieldDefinition\Reference;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Unit;
use Example\Entity;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition\Reference
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 * @uses \Ergebnis\FactoryBot\Strategy\DefaultStrategy
 */
final class ReferenceTest extends Unit\AbstractTestCase
{
    public function testResolvesToObjectCreatedByFixtureFactory(): void
    {
        $className = Entity\User::class;

        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $fixtureFactory->define($className);

        $fieldDefinition = new Reference($className);

        $resolved = $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );

        self::assertInstanceOf($className, $resolved);
    }
}
