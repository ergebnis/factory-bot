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

namespace Ergebnis\FactoryBot\Test\Unit\FieldDefinition;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Ergebnis\FactoryBot\Test;

#[\PHPUnit\Framework\Attributes\CoversClass(\Ergebnis\FactoryBot\FieldDefinition\Sequence::class)]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\Exception\InvalidSequence')]
#[\PHPUnit\Framework\Attributes\UsesClass('\Ergebnis\FactoryBot\FixtureFactory')]
final class SequenceTest extends Test\Unit\AbstractTestCase
{
    public function testConstructorRejectsValueWhenItIsMissingPercentDPlaceholder(): void
    {
        $faker = self::faker();

        $value = $faker->sentence();
        $initialNumber = $faker->randomNumber();

        $this->expectException(Exception\InvalidSequence::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to contain a placeholder "%%d", but "%s" does not',
            $value,
        ));

        new FieldDefinition\Sequence(
            $value,
            $initialNumber,
        );
    }

    public function testResolvesToValueWithPercentDReplacedWithSequentialNumber(): void
    {
        $faker = self::faker();

        $fixtureFactory = new FixtureFactory(
            self::entityManager(),
            $faker,
        );

        $value = '%d Why, hello - this is a nice thing, if you need it! %d';

        $initialNumber = $faker->numberBetween();

        $fieldDefinition = new FieldDefinition\Sequence(
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
