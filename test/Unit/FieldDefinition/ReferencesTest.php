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
    public function testOptionalRejectsInvalidCount(int $count): void
    {
        $this->expectException(Exception\InvalidCount::class);
        $this->expectExceptionMessage(\sprintf(
            'Count needs to be greater than or equal to 1, but %d is not.',
            $count
        ));

        References::optional(
            Fixture\FixtureFactory\Entity\User::class,
            $count
        );
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $count
     */
    public function testOptionalResolvesToArrayOfObjectsCreatedByFixtureFactory(int $count): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define($className);

        $fieldDefinition = References::optional(
            $className,
            $count
        );

        self::assertFalse($fieldDefinition->isRequired());

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertIsArray($resolved);
        self::assertCount($count, $resolved);
        self::assertContainsOnly($className, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intLessThanOne()
     *
     * @param int $count
     */
    public function testRequiredRejectsInvalidCount(int $count): void
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
    public function testRequiredResolvesToArrayOfObjectsCreatedByFixtureFactory(int $count): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            self::faker()
        );

        $fixtureFactory->define($className);

        $fieldDefinition = References::required(
            $className,
            $count
        );

        self::assertTrue($fieldDefinition->isRequired());

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertIsArray($resolved);
        self::assertCount($count, $resolved);
        self::assertContainsOnly($className, $resolved);
    }
}
