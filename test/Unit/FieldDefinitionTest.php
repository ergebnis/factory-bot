<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit;

use Ergebnis\FactoryBot;
use Example\Entity;
use Faker\Generator;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Count::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Exception\InvalidCount::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Exception\InvalidSequence::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Closure::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Optional::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Reference::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\References::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Sequence::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Value::class)]
final class FieldDefinitionTest extends AbstractTestCase
{
    public function testClosureReturnsClosure(): void
    {
        $closure = static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory): Entity\User {
            /** @var Entity\User $user */
            $user = $fixtureFactory->createOne(Entity\User::class);

            $user->renameTo(self::faker()->userName());

            return $user;
        };

        $fieldDefinition = FactoryBot\FieldDefinition::closure($closure);

        $expected = new FactoryBot\FieldDefinition\Closure($closure);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalClosureReturnsOptionalClosure(): void
    {
        $closure = static function (Generator $faker, FactoryBot\FixtureFactory $fixtureFactory): Entity\User {
            /** @var Entity\User $user */
            $user = $fixtureFactory->createOne(Entity\User::class);

            $user->renameTo(self::faker()->userName());

            return $user;
        };

        $fieldDefinition = FactoryBot\FieldDefinition::optionalClosure($closure);

        $expected = new FactoryBot\FieldDefinition\Optional(new FactoryBot\FieldDefinition\Closure($closure));

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferenceReturnsReference(): void
    {
        $className = Entity\User::class;

        $fieldDefinition = FactoryBot\FieldDefinition::reference($className);

        $expected = new FactoryBot\FieldDefinition\Reference($className);

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferenceReturnsReferenceWhenFieldDefinitionOverridesAreSpecified(): void
    {
        $className = Entity\User::class;

        $name = self::faker()->name();

        $fieldDefinition = FactoryBot\FieldDefinition::reference(
            $className,
            [
                'name' => FactoryBot\FieldDefinition::value($name),
            ],
        );

        $expected = new FactoryBot\FieldDefinition\Reference(
            $className,
            [
                'name' => FactoryBot\FieldDefinition::value($name),
            ],
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalReferenceReturnsOptionalReference(): void
    {
        $className = Entity\User::class;

        $fieldDefinition = FactoryBot\FieldDefinition::optionalReference($className);

        $expected = new FactoryBot\FieldDefinition\Optional(new FactoryBot\FieldDefinition\Reference($className));

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testOptionalReferenceReturnsOptionalReferenceWhenFieldDefinitionOverridesAreSpecified(): void
    {
        $className = Entity\User::class;

        $name = self::faker()->name();

        $fieldDefinition = FactoryBot\FieldDefinition::optionalReference(
            $className,
            [
                'name' => FactoryBot\FieldDefinition::value($name),
            ],
        );

        $expected = new FactoryBot\FieldDefinition\Optional(new FactoryBot\FieldDefinition\Reference(
            $className,
            [
                'name' => FactoryBot\FieldDefinition::value($name),
            ],
        ));

        self::assertEquals($expected, $fieldDefinition);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testReferencesReturnsReferencesWhenCountIsSpecified(int $value): void
    {
        $className = Entity\User::class;

        $fieldDefinition = FactoryBot\FieldDefinition::references(
            $className,
            FactoryBot\Count::exact($value),
        );

        $expected = new FactoryBot\FieldDefinition\References(
            $className,
            FactoryBot\Count::exact($value),
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testReferencesReturnsReferencesWhenFieldDefinitionOverridesAreSpecified(): void
    {
        $faker = self::faker();

        $className = Entity\User::class;
        $count = FactoryBot\Count::exact($faker->numberBetween(1, 5));

        $name = $faker->name();

        $fieldDefinition = FactoryBot\FieldDefinition::references(
            $className,
            $count,
            [
                'name' => FactoryBot\FieldDefinition::value($name),
            ],
        );

        $expected = new FactoryBot\FieldDefinition\References(
            $className,
            $count,
            [
                'name' => FactoryBot\FieldDefinition::value($name),
            ],
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    public function testSequenceRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence();
        $initialNumber = $faker->randomNumber();

        $this->expectException(FactoryBot\Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        ));

        FactoryBot\FieldDefinition::sequence(
            $value,
            $initialNumber,
        );
    }

    public function testSequenceReturnsSequenceWhenValueContainsPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FactoryBot\FieldDefinition::sequence($value);

        $expected = new FactoryBot\FieldDefinition\Sequence(
            $value,
            1,
        );

        self::assertEquals($expected, $fieldDefinition);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
    public function testSequenceReturnsSequenceWhenValueContainsPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FactoryBot\FieldDefinition::sequence(
            $value,
            $initialNumber,
        );

        $expected = new FactoryBot\FieldDefinition\Sequence(
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

        $this->expectException(FactoryBot\Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        ));

        FactoryBot\FieldDefinition::optionalSequence(
            $value,
            $initialNumber,
        );
    }

    public function testOptionalSequenceReturnsOptionalSequenceWhenValueContainsPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FactoryBot\FieldDefinition::optionalSequence($value);

        $expected = new FactoryBot\FieldDefinition\Optional(new FactoryBot\FieldDefinition\Sequence(
            $value,
            1,
        ));

        self::assertEquals($expected, $fieldDefinition);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\IntProvider::class, 'arbitrary')]
    public function testOptionalSequenceReturnsOptionalSequenceWhenValueContainsPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fieldDefinition = FactoryBot\FieldDefinition::optionalSequence(
            $value,
            $initialNumber,
        );

        $expected = new FactoryBot\FieldDefinition\Optional(new FactoryBot\FieldDefinition\Sequence(
            $value,
            $initialNumber,
        ));

        self::assertEquals($expected, $fieldDefinition);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\ValueProvider::class, 'arbitrary')]
    public function testValueReturnsValue(mixed $value): void
    {
        $fieldDefinition = FactoryBot\FieldDefinition::value($value);

        $expected = new FactoryBot\FieldDefinition\Value($value);

        self::assertEquals($expected, $fieldDefinition);
    }

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\ValueProvider::class, 'arbitrary')]
    public function testOptionalValueReturnsOptionalValue(mixed $value): void
    {
        $fieldDefinition = FactoryBot\FieldDefinition::optionalValue($value);

        $expected = new FactoryBot\FieldDefinition\Optional(new FactoryBot\FieldDefinition\Value($value));

        self::assertEquals($expected, $fieldDefinition);
    }
}
