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

namespace Ergebnis\FactoryBot\Test\Unit;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition
 *
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Closure
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Reference
 * @uses \Ergebnis\FactoryBot\FieldDefinition\References
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Sequence
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 */
final class FieldDefinitionTest extends AbstractTestCase
{
    public function testClosureReturnsClosure(): void
    {
        $closure = static function (FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\User {
            /** @var Fixture\FixtureFactory\Entity\User $user */
            $user = $fixtureFactory->create(Fixture\FixtureFactory\Entity\User::class);

            $user->renameTo(self::faker()->userName);

            return $user;
        };

        $fieldDefinition = FieldDefinition::closure($closure);

        $expected = FieldDefinition\Closure::required($closure);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferenceReturnsReference(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::reference($className);

        $expected = FieldDefinition\Reference::required($className);

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intLessThanOne()
     *
     * @param int $count
     */
    public function testReferencesThrowsInvalidCountExceptionWhenCountIsLessThanOne(int $count): void
    {
        $className = self::class;

        $this->expectException(Exception\InvalidCount::class);

        FieldDefinition::references(
            $className,
            $count
        );
    }

    public function testReferencesReturnsReferencesWhenCountIsNotSpecified(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::references($className);

        $expected = FieldDefinition\References::required(
            $className,
            1
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $count
     */
    public function testReferencesReturnsReferencesWhenCountIsSpecified(int $count): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::references(
            $className,
            $count
        );

        $expected = FieldDefinition\References::required(
            $className,
            $count
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testSequenceReturnsSequenceWhenValueContainsPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FieldDefinition::sequence($value);

        $expected = FieldDefinition\Sequence::required(
            $value,
            1
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $initialNumber
     */
    public function testSequenceReturnsSequenceWhenValueContainsPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FieldDefinition::sequence(
            $value,
            $initialNumber
        );

        $expected = FieldDefinition\Sequence::required(
            $value,
            $initialNumber
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testSequenceReturnsSequenceWhenValueDoesNotContainPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'user-';

        $fieldDefinition = FieldDefinition::sequence($value);

        $expected = FieldDefinition\Sequence::required(
            $value . '%d',
            1
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $initialNumber
     */
    public function testSequenceReturnsSequenceWhenValueDoesNotContainPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'user-';

        $fieldDefinition = FieldDefinition::sequence(
            $value,
            $initialNumber
        );

        $expected = FieldDefinition\Sequence::required(
            $value . '%d',
            $initialNumber
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::arbitrary()
     *
     * @param mixed $value
     */
    public function testValueReturnsValue($value): void
    {
        $fieldDefinition = FieldDefinition::value($value);

        $expected = FieldDefinition\Value::required($value);

        self::assertEquals($expected, $fieldDefinition);
    }
}
