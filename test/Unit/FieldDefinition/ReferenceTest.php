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

use Ergebnis\FactoryBot\FieldDefinition\Reference;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition\Reference
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class ReferenceTest extends AbstractTestCase
{
    public function testOptionalResolvesToObjectCreatedByFixtureFactory(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define($className);

        $fieldDefinition = Reference::optional($className);

        self::assertFalse($fieldDefinition->isRequired());

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertInstanceOf($className, $resolved);
    }

    public function testRequiredResolvesToObjectCreatedByFixtureFactory(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define($className);

        $fieldDefinition = Reference::required($className);

        self::assertTrue($fieldDefinition->isRequired());

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertInstanceOf($className, $resolved);
    }
}
