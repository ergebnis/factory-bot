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
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class ReferenceTest extends AbstractTestCase
{
    public function testResolveReturnsObjectFromFixtureFactory(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity($className);

        $fieldDefinition = new Reference($className);

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertInstanceOf($className, $resolved);
    }
}
