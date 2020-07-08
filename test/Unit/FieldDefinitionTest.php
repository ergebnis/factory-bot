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

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Fixture;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition
 *
 * @uses \Ergebnis\FactoryBot\Count
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\Exception\InvalidSequence
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Closure
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Optional
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
            $user = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

            $user->renameTo(self::faker()->userName);

            return $user;
        };

        $fieldDefinition = FieldDefinition::closure($closure);

        $expected = new FieldDefinition\Closure($closure);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalClosureReturnsOptionalClosure(): void
    {
        $closure = static function (FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\User {
            /** @var Fixture\FixtureFactory\Entity\User $user */
            $user = $fixtureFactory->createOne(Fixture\FixtureFactory\Entity\User::class);

            $user->renameTo(self::faker()->userName);

            return $user;
        };

        $fieldDefinition = FieldDefinition::optionalClosure($closure);

        $expected = new FieldDefinition\Optional(new FieldDefinition\Closure($closure));

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferenceReturnsRequiredReference(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::reference($className);

        $expected = new FieldDefinition\Reference($className);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalReferenceReturnsOptionalReference(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;

        $fieldDefinition = FieldDefinition::optionalReference($className);

        $expected = new FieldDefinition\Optional(new FieldDefinition\Reference($className));

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferencesReturnsRequiredReferencesWhenCountIsNotSpecified(): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;
        $count = new Count(1);

        $fieldDefinition = FieldDefinition::references($className);

        $expected = new FieldDefinition\References(
            $className,
            $count
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $value
     */
    public function testReferencesReturnsRequiredReferencesWhenCountIsSpecified(int $value): void
    {
        $className = Fixture\FixtureFactory\Entity\User::class;
        $count = new Count($value);

        $fieldDefinition = FieldDefinition::references(
            $className,
            $count
        );

        $expected = new FieldDefinition\References(
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

        $expected = new FieldDefinition\Sequence(
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

        $expected = new FieldDefinition\Sequence(
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

        $expected = new FieldDefinition\Optional(new FieldDefinition\Sequence(
            $value,
            1
        ));

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

        $expected = new FieldDefinition\Optional(new FieldDefinition\Sequence(
            $value,
            $initialNumber
        ));

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

        $expected = new FieldDefinition\Value($value);

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

        $expected = new FieldDefinition\Optional(new FieldDefinition\Value($value));

        self::assertEquals($expected, $fieldDefinition);
    }
}
