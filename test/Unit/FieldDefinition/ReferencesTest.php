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

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition\References;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition\References
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class ReferencesTest extends AbstractTestCase
{
    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intLessThanOne()
     *
     * @param int $count
     */
    public function testConstructorRejectsInvalidCount(int $count): void
    {
        $this->expectException(Exception\InvalidCount::class);
        $this->expectExceptionMessage(\sprintf(
            'Count needs to be greater than or equal to 1, but %d is not.',
            $count
        ));

        References::required(
            Fixture\FixtureFactory\Entity\User::class,
            $count
        );
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $count
     */
    public function testResolveReturnsObjectsFromFixtureFactory(int $count): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity($className);

        $fieldDefinition = References::required(
            $className,
            $count
        );

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertCount($count, $resolved);
        self::assertContainsOnly($className, $resolved);
    }
}
