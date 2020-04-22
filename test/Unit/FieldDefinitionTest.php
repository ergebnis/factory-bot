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
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Closure
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Reference
 * @uses \Ergebnis\FactoryBot\FieldDefinition\References
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Sequence
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class FieldDefinitionTest extends AbstractTestCase
{
    public function testClosureResolvesToTheReturnValueOfClosureInvokedWithFixtureFactory(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $closure = static function (FixtureFactory $fixtureFactory): Fixture\FixtureFactory\Entity\User {
            /** @var Fixture\FixtureFactory\Entity\User $user */
            $user = $fixtureFactory->get(Fixture\FixtureFactory\Entity\User::class);

            $user->renameTo(self::faker()->userName);

            return $user;
        };

        $fieldDefinition = FieldDefinition::closure($closure);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\User::class, $fieldDefinition->resolve($fixtureFactory));
    }

    public function testReferenceResolvesToInstanceOfReferencedClass(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::reference(Fixture\FixtureFactory\Entity\User::class);

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertInstanceOf(Fixture\FixtureFactory\Entity\User::class, $resolved);
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

    public function testReferencesResolvesToAnArrayOfOneInstancesOfReferencedClassWhenCountIsNotSpecified(): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::references(Fixture\FixtureFactory\Entity\User::class);

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertIsArray($resolved);
        self::assertCount(1, $resolved);
        self::assertContainsOnly(Fixture\FixtureFactory\Entity\User::class, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $count
     */
    public function testReferencesResolvesToAnArrayOfCountInstancesOfReferencedClassWhenCountIsSpecified(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::references(
            Fixture\FixtureFactory\Entity\User::class,
            $count
        );

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertIsArray($resolved);
        self::assertCount($count, $resolved);
        self::assertContainsOnly(Fixture\FixtureFactory\Entity\User::class, $resolved);
    }

    public function testSequenceResolvesToValueWithPlaceholderReplacedWithSequentialNumberWhenValueContainsPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($value);

        $expected = static function (int $number): string {
            return \sprintf(
                'there-is-no-difference-between-%d-and-%d',
                $number,
                $number
            );
        };

        self::assertSame($expected(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(3), $fieldDefinition->resolve($fixtureFactory));
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $initialNumber
     */
    public function testSequenceResolvesToValueWithPlaceholderReplacedWithSequentialNumberWhenValueContainsPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'there-is-no-difference-between-%d-and-%d';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $value,
            $initialNumber
        );

        $expected = static function (int $number) use ($initialNumber): string {
            return \sprintf(
                'there-is-no-difference-between-%d-and-%d',
                $number + $initialNumber - 1,
                $number + $initialNumber - 1
            );
        };

        self::assertSame($expected(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(3), $fieldDefinition->resolve($fixtureFactory));
    }

    public function testSequenceResolvesToValueSuffixedWithSequentialNumberWhenValueDoesNotContainPlaceholderAndInitialNumberIsNotSpecified(): void
    {
        $value = 'user-';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($value);

        $expected = static function (int $number): string {
            return \sprintf(
                'user-%d',
                $number
            );
        };

        self::assertSame($expected(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(3), $fieldDefinition->resolve($fixtureFactory));
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $initialNumber
     */
    public function testSequenceResolvesToValueSuffixedWithSequentialNumberWhenValueDoesNotContainPlaceholderAndInitialNumberIsSpecified(int $initialNumber): void
    {
        $value = 'user-';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $value,
            $initialNumber
        );

        $expected = static function (int $number) use ($initialNumber): string {
            return \sprintf(
                'user-%d',
                $number + $initialNumber - 1
            );
        };

        self::assertSame($expected(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(3), $fieldDefinition->resolve($fixtureFactory));
    }

    /**
     * @dataProvider provideArbitraryValue
     *
     * @param mixed $value
     */
    public function testValueResolvesToValue($value): void
    {
        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fieldDefinition = FieldDefinition::value($value);

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertSame($value, $resolved);
    }

    public function provideArbitraryValue(): \Generator
    {
        $faker = self::faker();

        $values = [
            'array' => $faker->words,
            'bool-false' => false,
            'bool-true' => true,
            'float' => $faker->randomFloat(),
            'int' => $faker->numberBetween(),
            'object' => new \stdClass(),
            'resource' => \fopen(__FILE__, 'rb'),
            'string' => $faker->sentence,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
