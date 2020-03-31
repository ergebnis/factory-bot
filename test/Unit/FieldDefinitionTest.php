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
use Ergebnis\Test\Util\Helper;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition
 *
 * @uses \Ergebnis\FactoryBot\EntityDefinition
 * @uses \Ergebnis\FactoryBot\Exception\InvalidCount
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class FieldDefinitionTest extends AbstractTestCase
{
    use Helper;

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

    public function testSequenceResolvesToReturnValueOfCallableInvokedWithSequentialNumberWhenSequenceIsCallableAndFirstNumberIsNotSpecified(): void
    {
        $callable = [
            self::class,
            'exampleCallable',
        ];

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($callable);

        $expected = static function (int $number) use ($callable): string {
            return \call_user_func(
                $callable,
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
     * @param int $firstNumber
     */
    public function testSequenceResolvesToReturnValueOfCallableInvokedWithSequentialNumberWhenSequenceIsCallableAndFirstNumberIsSpecified(int $firstNumber): void
    {
        $callable = [
            self::class,
            'exampleCallable',
        ];

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $callable,
            $firstNumber
        );

        $expected = static function (int $number) use ($callable, $firstNumber): string {
            return \call_user_func(
                $callable,
                $number + $firstNumber - 1
            );
        };

        self::assertSame($expected(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(3), $fieldDefinition->resolve($fixtureFactory));
    }

    public static function exampleCallable(int $number): string
    {
        return \sprintf(
            'number-%d-is-an-integer',
            $number
        );
    }

    public function testSequenceResolvesToTheReturnValueOfClosureInvokedWithSequentialNumberWhenSequenceIsClosureAndFirstNumberIsNotSpecified(): void
    {
        $closure = static function (int $number): string {
            return \sprintf(
                'number-%d-is-ok',
                $number
            );
        };

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($closure);

        self::assertSame($closure(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($closure(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($closure(3), $fieldDefinition->resolve($fixtureFactory));
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intGreaterThanOne()
     *
     * @param int $firstNumber
     */
    public function testSequenceResolvesToTheReturnValueOfClosureInvokedWithSequentialNumberWhenSequenceIsClosureAndFirstNumberIsSpecified(int $firstNumber): void
    {
        $closure = static function (int $number): string {
            return \sprintf(
                'number-%d-is-ok',
                $number
            );
        };

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $closure,
            $firstNumber
        );

        $expected = static function (int $number) use ($firstNumber): string {
            return \sprintf(
                'number-%d-is-ok',
                $number + $firstNumber - 1
            );
        };

        self::assertSame($expected(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(3), $fieldDefinition->resolve($fixtureFactory));
    }

    public function testSequenceResolvesToAStringWithPlaceholderReplacedWithSequentialNumberWhenSequenceIsStringThatContainsPlaceholderAndFirstNumberIsNotSpecified(): void
    {
        $string = 'there-is-no-difference-between-%d-and-%d';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($string);

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
     * @param int $firstNumber
     */
    public function testSequenceResolvesToAStringWithPlaceholderReplacedWithSequentialNumberWhenSequenceIsStringThatContainsPlaceholderAndFirstNumberIsSpecified(int $firstNumber): void
    {
        $string = 'there-is-no-difference-between-%d-and-%d';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $string,
            $firstNumber
        );

        $expected = static function (int $number) use ($firstNumber): string {
            return \sprintf(
                'there-is-no-difference-between-%d-and-%d',
                $number + $firstNumber - 1,
                $number + $firstNumber - 1
            );
        };

        self::assertSame($expected(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(3), $fieldDefinition->resolve($fixtureFactory));
    }

    public function testSequenceResolvesToAStringSuffixedWithSequentialNumberSequenceIsStringThatDoesNotContainPlaceholderWhenFirstNumberIsNotSpecified(): void
    {
        $string = 'user-';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($string);

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
     * @param int $firstNumber
     */
    public function testSequenceResolvesToAStringSuffixedWithSequentialNumberSequenceIsStringThatDoesNotContainPlaceholderWhenFirstNumberIsSpecified(int $firstNumber): void
    {
        $string = 'user-';

        $fixtureFactory = new FixtureFactory(self::entityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $string,
            $firstNumber
        );

        $expected = static function (int $number) use ($firstNumber): string {
            return \sprintf(
                'user-%d',
                $number + $firstNumber - 1
            );
        };

        self::assertSame($expected(1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(2), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected(3), $fieldDefinition->resolve($fixtureFactory));
    }
}
