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
use Ergebnis\FactoryBot\FieldDefinition\Sequence;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\FieldDefinition\Sequence
 *
 * @uses \Ergebnis\FactoryBot\Exception\InvalidSequence
 * @uses \Ergebnis\FactoryBot\FixtureFactory
 */
final class SequenceTest extends AbstractTestCase
{
    public function testOptionalRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence;
        $initialNumber = $faker->randomNumber();

        $this->expectException(Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value
        ));

        Sequence::optional(
            $value,
            $initialNumber
        );
    }

    public function testOptionalResolvesToValueWithPercentDReplacedWithSequentialNumber(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $value = '%d Why, hello - this is a nice thing, if you need it! %d';

        $initialNumber = $faker->numberBetween();

        $fieldDefinition = Sequence::optional(
            $value,
            $initialNumber
        );

        self::assertFalse($fieldDefinition->isRequired());

        $expected = static function (int $sequentialNumber): string {
            return \sprintf(
                '%d Why, hello - this is a nice thing, if you need it! %d',
                $sequentialNumber,
                $sequentialNumber
            );
        };

        self::assertSame($expected($initialNumber), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected($initialNumber + 1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected($initialNumber + 2), $fieldDefinition->resolve($fixtureFactory));
    }

    public function testRequiredRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence;
        $initialNumber = $faker->randomNumber();

        $this->expectException(Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value
        ));

        Sequence::required(
            $value,
            $initialNumber
        );
    }

    public function testRequiredResolvesToValueWithPercentDReplacedWithSequentialNumber(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker
        );

        $value = '%d Why, hello - this is a nice thing, if you need it! %d';

        $initialNumber = $faker->numberBetween();

        $fieldDefinition = Sequence::required(
            $value,
            $initialNumber
        );

        self::assertTrue($fieldDefinition->isRequired());

        $expected = static function (int $sequentialNumber): string {
            return \sprintf(
                '%d Why, hello - this is a nice thing, if you need it! %d',
                $sequentialNumber,
                $sequentialNumber
            );
        };

        self::assertSame($expected($initialNumber), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected($initialNumber + 1), $fieldDefinition->resolve($fixtureFactory));
        self::assertSame($expected($initialNumber + 2), $fieldDefinition->resolve($fixtureFactory));
    }
}
