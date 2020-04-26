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
    public function testClosureReturnsRequiredClosure(): void
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

    public function testOptionalClosureReturnsOptionalClosure(): void
    {
        $closure = static function (FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\User {
            /** @var Fixture\FixtureFactory\Entity\User $user */
            $user = $fixtureFactory->create(Fixture\FixtureFactory\Entity\User::class);

            $user->renameTo(self::faker()->userName);

            return $user;
        };

        $fieldDefinition = FieldDefinition::optionalClosure($closure);

        $expected = FieldDefinition\Closure::optional($closure);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferenceReturnsRequiredReference(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::reference($className);

        $expected = FieldDefinition\Reference::required($className);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalReferenceReturnsOptionalReference(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::optionalReference($className);

        $expected = FieldDefinition\Reference::optional($className);

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

    public function testReferencesReturnsRequiredReferencesWhenCountIsNotSpecified(): void
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
    public function testReferencesReturnsRequiredReferencesWhenCountIsSpecified(int $count): void
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

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intLessThanOne()
     *
     * @param int $count
     */
    public function testOptionalReferencesThrowsInvalidCountExceptionWhenCountIsLessThanOne(int $count): void
    {
        $className = self::class;

        $this->expectException(Exception\InvalidCount::class);

        FieldDefinition::optionalReferences(
            $className,
            $count
        );
    }

    public function testOptionalReferencesReturnsOptionalReferencesWhenCountIsNotSpecified(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::optionalReferences($className);

        $expected = FieldDefinition\References::optional(
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
    public function testOptionalReferencesReturnsOptionalReferencesWhenCountIsSpecified(int $count): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::optionalReferences(
            $className,
            $count
        );

        $expected = FieldDefinition\References::optional(
            $className,
            $count
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testSequenceRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence;
        $initialNumber = $faker->randomNumber();

        $this->expectException(Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value
        ));

        FieldDefinition::sequence(
            $value,
            $initialNumber
        );
    }

    public function testSequenceReturnsRequiredSequenceWhenValueContainsPlaceholderAndInitialNumberIsNotSpecified(): void
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
    public function testSequenceReturnsRequiredSequenceWhenValueContainsPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
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

    public function testOptionalSequenceRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence;
        $initialNumber = $faker->randomNumber();

        $this->expectException(Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value
        ));

        FieldDefinition::optionalSequence(
            $value,
            $initialNumber
        );
    }

    public function testOptionalSequenceReturnsOptionalSequenceWhenValueContainsPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FieldDefinition::optionalSequence($value);

        $expected = FieldDefinition\Sequence::optional(
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
    public function testOptionalSequenceReturnsOptionalSequenceWhenValueContainsPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FieldDefinition::optionalSequence(
            $value,
            $initialNumber
        );

        $expected = FieldDefinition\Sequence::optional(
            $value,
            $initialNumber
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::arbitrary()
     *
     * @param mixed $value
     */
    public function testValueReturnsRequiredValue($value): void
    {
        $fieldDefinition = FieldDefinition::value($value);

        $expected = FieldDefinition\Value::required($value);

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::arbitrary()
     *
     * @param mixed $value
     */
    public function testOptionalValueReturnsOptionalValue($value): void
    {
        $fieldDefinition = FieldDefinition::optionalValue($value);

        $expected = FieldDefinition\Value::optional($value);

        self::assertEquals($expected, $fieldDefinition);
    }
}
