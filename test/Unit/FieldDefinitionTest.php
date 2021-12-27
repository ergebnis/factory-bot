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

namespace Ergebnis\FactoryBot\Test\Unit;

use Ergebnis\FactoryBot\Count;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Example\Entity;
use Faker\Generator;

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
    public function testClosureReturnsClosure(): void
    {
        $closure = static function (Generator $faker, FixtureFactory $fixtureFactory): Entity\User {
            /** @var Entity\User $user */
            $user = $fixtureFactory->createOne(Entity\User::class);

            $user->renameTo(self::faker()->userName);

            return $user;
        };

        $fieldDefinition = FieldDefinition::closure($closure);

        $expected = new FieldDefinition\Closure($closure);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalClosureReturnsOptionalClosure(): void
    {
        $closure = static function (Generator $faker, FixtureFactory $fixtureFactory): Entity\User {
            /** @var Entity\User $user */
            $user = $fixtureFactory->createOne(Entity\User::class);

            $user->renameTo(self::faker()->userName);

            return $user;
        };

        $fieldDefinition = FieldDefinition::optionalClosure($closure);

        $expected = new FieldDefinition\Optional(new FieldDefinition\Closure($closure));

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferenceReturnsReference(): void
    {
        $className = Entity\User::class;

        $fieldDefinition = FieldDefinition::reference($className);

        $expected = new FieldDefinition\Reference($className);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferenceReturnsReferenceWhenFieldDefinitionOverridesAreSpecified(): void
    {
        $className = Entity\User::class;

        $name = self::faker()->name();

        $fieldDefinition = FieldDefinition::reference(
            $className,
            [
                'name' => FieldDefinition::value($name),
            ],
        );

        $expected = new FieldDefinition\Reference(
            $className,
            [
                'name' => FieldDefinition::value($name),
            ],
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalReferenceReturnsOptionalReference(): void
    {
        $className = Entity\User::class;

        $fieldDefinition = FieldDefinition::optionalReference($className);

        $expected = new FieldDefinition\Optional(new FieldDefinition\Reference($className));

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalReferenceReturnsOptionalReferenceWhenFieldDefinitionOverridesAreSpecified(): void
    {
        $className = Entity\User::class;

        $name = self::faker()->name();

        $fieldDefinition = FieldDefinition::optionalReference(
            $className,
            [
                'name' => FieldDefinition::value($name),
            ],
        );

        $expected = new FieldDefinition\Optional(new FieldDefinition\Reference(
            $className,
            [
                'name' => FieldDefinition::value($name),
            ],
        ));

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testReferencesReturnsReferencesWhenCountIsSpecified(int $value): void
    {
        $className = Entity\User::class;

        $fieldDefinition = FieldDefinition::references(
            $className,
            Count::exact($value),
        );

        $expected = new FieldDefinition\References(
            $className,
            Count::exact($value),
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferencesReturnsReferencesWhenFieldDefinitionOverridesAreSpecified(): void
    {
        $faker = self::faker();

        $className = Entity\User::class;
        $count = Count::exact($faker->numberBetween(1, 5));

        $name = $faker->name();

        $fieldDefinition = FieldDefinition::references(
            $className,
            $count,
            [
                'name' => FieldDefinition::value($name),
            ],
        );

        $expected = new FieldDefinition\References(
            $className,
            $count,
            [
                'name' => FieldDefinition::value($name),
            ],
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testSequenceRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence();
        $initialNumber = $faker->randomNumber();

        $this->expectException(Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        ));

        FieldDefinition::sequence(
            $value,
            $initialNumber,
        );
    }

    public function testSequenceReturnsSequenceWhenValueContainsPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FieldDefinition::sequence($value);

        $expected = new FieldDefinition\Sequence(
            $value,
            1,
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::greaterThanOrEqualToZero()
     */
    public function testSequenceReturnsSequenceWhenValueContainsPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FieldDefinition::sequence(
            $value,
            $initialNumber,
        );

        $expected = new FieldDefinition\Sequence(
            $value,
            $initialNumber,
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalSequenceRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence();
        $initialNumber = $faker->randomNumber();

        $this->expectException(Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        ));

        FieldDefinition::optionalSequence(
            $value,
            $initialNumber,
        );
    }

    public function testOptionalSequenceReturnsOptionalSequenceWhenValueContainsPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FieldDefinition::optionalSequence($value);

        $expected = new FieldDefinition\Optional(new FieldDefinition\Sequence(
            $value,
            1,
        ));

        self::assertEquals($expected, $fieldDefinition);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\IntProvider::arbitrary()
     */
    public function testOptionalSequenceReturnsOptionalSequenceWhenValueContainsPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FieldDefinition::optionalSequence(
            $value,
            $initialNumber,
        );

        $expected = new FieldDefinition\Optional(new FieldDefinition\Sequence(
            $value,
            $initialNumber,
        ));

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
