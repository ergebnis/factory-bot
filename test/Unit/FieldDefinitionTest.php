<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2023 Andreas Möller
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

#[\PHPUnit\Framework\Attributes\CoversClass(\Ergebnis\FactoryBot\FieldDefinition::class)]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\Count')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\Exception\InvalidCount')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\Exception\InvalidSequence')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\Closure')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\Optional')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\Reference')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\References')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\Sequence')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FieldDefinition\Value')]
final class FieldDefinitionTest extends AbstractTestCase
{
    public function testClosureReturnsClosure(): void
    {
        $closure = static function (Generator $faker, FixtureFactory $fixtureFactory): Entity\User {
            /** @var Entity\User $user */
            $user = $fixtureFactory->createOne(Entity\User::class);

            $user->renameTo(self::faker()->userName());

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

            $user->renameTo(self::faker()->userName());

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

    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
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

    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\IntProvider::class, 'greaterThanOrEqualToZero')]
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

    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\IntProvider::class, 'arbitrary')]
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

    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::class, 'arbitrary')]
    public function testValueReturnsValue(mixed $value): void
    {
        $fieldDefinition = FieldDefinition::value($value);

        $expected = new FieldDefinition\Value($value);

        self::assertEquals($expected, $fieldDefinition);
    }

    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::class, 'arbitrary')]
    public function testOptionalValueReturnsOptionalValue(mixed $value): void
    {
        $fieldDefinition = FieldDefinition::optionalValue($value);

        $expected = new FieldDefinition\Optional(new FieldDefinition\Value($value));

        self::assertEquals($expected, $fieldDefinition);
    }
}
