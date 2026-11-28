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

namespace Ergebnis\FactoryBot\Test\Unit\FieldDefinition;

use Ergebnis\FactoryBot;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\FieldDefinition\Sequence::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Exception\InvalidSequence::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FixtureFactory::class)]
final class SequenceTest extends FactoryBot\Test\Unit\AbstractTestCase
{
    public function testConstructorRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence();
        $initialNumber = $faker->randomNumber();

        $this->expectException(FactoryBot\Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        ));

        new FactoryBot\FieldDefinition\Sequence(
            $value,
            $initialNumber,
        );
    }

    public function testResolvesToValueWithPercentDReplacedWithSequentialNumber(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FactoryBot\FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $value = '%d Why, hello - this is a nice thing, if you need it! %d';

        $initialNumber = $faker->numberBetween();

        $fieldDefinition = new FactoryBot\FieldDefinition\Sequence(
            $value,
            $initialNumber,
        );

        $expected = static function (int $sequentialNumber): string {
            return \sprintf(
                '%d Why, hello - this is a nice thing, if you need it! %d',
                $sequentialNumber,
                $sequentialNumber,
            );
        };

        self::assertSame($expected($initialNumber), $fieldDefinition->resolve($faker, $fixtureFactory));
        self::assertSame($expected($initialNumber + 1), $fieldDefinition->resolve($faker, $fixtureFactory));
        self::assertSame($expected($initialNumber + 2), $fieldDefinition->resolve($faker, $fixtureFactory));
    }
}
