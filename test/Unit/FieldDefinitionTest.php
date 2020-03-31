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
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

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
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::references(Fixture\FixtureFactory\Entity\User::class);

        $resolved = $fieldDefinition->resolve($fixtureFactory);

        self::assertIsArray($resolved);
        self::assertCount(1, $resolved);
        self::assertContainsOnly(Fixture\FixtureFactory\Entity\User::class, $resolved);
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intBetweenOneAndFive()
     *
     * @param int $count
     */
    public function testReferencesResolvesToAnArrayOfCountInstancesOfReferencedClassWhenCountIsSpecified(int $count): void
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

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

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($callable);

        self::assertSame('number-1-is-an-integer', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('number-2-is-an-integer', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('number-3-is-an-integer', $fieldDefinition->resolve($fixtureFactory));
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intBetweenOneAndFive()
     *
     * @param int $firstNumber
     */
    public function testSequenceResolvesToReturnValueOfCallableInvokedWithSequentialNumberWhenSequenceIsCallableAndFirstNumberIsSpecified(int $firstNumber): void
    {
        $callable = [
            self::class,
            'exampleCallable',
        ];

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $callable,
            $firstNumber
        );

        self::assertSame('number-' . $firstNumber . '-is-an-integer', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('number-' . ($firstNumber + 1) . '-is-an-integer', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('number-' . ($firstNumber + 2) . '-is-an-integer', $fieldDefinition->resolve($fixtureFactory));
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

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($closure);

        self::assertSame('number-1-is-ok', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('number-2-is-ok', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('number-3-is-ok', $fieldDefinition->resolve($fixtureFactory));
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intBetweenOneAndFive()
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

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $closure,
            $firstNumber
        );

        self::assertSame('number-' . $firstNumber . '-is-ok', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('number-' . ($firstNumber + 1) . '-is-ok', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('number-' . ($firstNumber + 2) . '-is-ok', $fieldDefinition->resolve($fixtureFactory));
    }

    public function testSequenceResolvesToAStringWithPlaceholderReplacedWithSequentialNumberWhenSequenceIsStringThatContainsPlaceholderAndFirstNumberIsNotSpecified(): void
    {
        $string = 'there-is-no-difference-between-%d-and-%d';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($string);

        self::assertSame('there-is-no-difference-between-1-and-1', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('there-is-no-difference-between-2-and-2', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('there-is-no-difference-between-3-and-3', $fieldDefinition->resolve($fixtureFactory));
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intBetweenOneAndFive()
     *
     * @param int $firstNumber
     */
    public function testSequenceResolvesToAStringWithPlaceholderReplacedWithSequentialNumberWhenSequenceIsStringThatContainsPlaceholderAndFirstNumberIsSpecified(int $firstNumber): void
    {
        $string = 'there-is-no-difference-between-%d-and-%d';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $string,
            $firstNumber
        );

        self::assertSame('there-is-no-difference-between-' . $firstNumber . '-and-' . $firstNumber, $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('there-is-no-difference-between-' . ($firstNumber + 1) . '-and-' . ($firstNumber + 1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('there-is-no-difference-between-' . ($firstNumber + 2) . '-and-' . ($firstNumber + 2), $fieldDefinition->resolve($fixtureFactory));
    }

    public function testSequenceResolvesToAStringSuffixedWithSequentialNumberSequenceIsStringThatDoesNotContainPlaceholderWhenFirstNumberIsNotSpecified(): void
    {
        $string = 'user-';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence($string);

        self::assertSame('user-1', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('user-2', $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('user-3', $fieldDefinition->resolve($fixtureFactory));
    }

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\NumberProvider::intBetweenOneAndFive()
     *
     * @param int $firstNumber
     */
    public function testSequenceResolvesToAStringSuffixedWithSequentialNumberSequenceIsStringThatDoesNotContainPlaceholderWhenFirstNumberIsSpecified(int $firstNumber): void
    {
        $string = 'user-';

        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Fixture\FixtureFactory\Entity\User::class);

        $fieldDefinition = FieldDefinition::sequence(
            $string,
            $firstNumber
        );

        self::assertSame('user-' . $firstNumber, $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('user-' . ($firstNumber + 1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame('user-' . ($firstNumber + 2), $fieldDefinition->resolve($fixtureFactory));
    }
}
