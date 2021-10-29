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

namespace Ergebnis\FactoryBot\Test\Unit\Exception;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\Test\Util;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\Exception\InvalidFieldNames
 */
final class InvalidFieldNamesTest extends Framework\TestCase
{
    use Util\Helper;

    public function testNotFoundInReturnsExceptionWhenOnlyOneFieldNameIsProvided(): void
    {
        $faker = self::faker();

        $className = $faker->word;
        $fieldName = $faker->word;

        $exception = Exception\InvalidFieldNames::notFoundIn(
            $className,
            $fieldName,
        );

        $message = \sprintf(
            'Entity "%s" does not have a field with the name "%s".',
            $className,
            $fieldName,
        );

        self::assertInstanceOf(Exception\InvalidFieldNames::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }

    public function testNotFoundInReturnsExceptionWhenMoreThanOneFieldNameIsProvided(): void
    {
        $faker = self::faker();

        $className = $faker->word;

        /** @var string[] $fieldNames */
        $fieldNames = $faker->words(10);

        $exception = Exception\InvalidFieldNames::notFoundIn(
            $className,
            ...$fieldNames,
        );

        $sortedFieldNames = $fieldNames;

        \natsort($sortedFieldNames);

        $message = \sprintf(
            'Entity "%s" does not have fields with the names "%s".',
            $className,
            \implode('", "', $sortedFieldNames),
        );

        self::assertInstanceOf(Exception\InvalidFieldNames::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
        self::assertInstanceOf(Exception\Exception::class, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }
}
