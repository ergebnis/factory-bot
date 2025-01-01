<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit\Exception;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Exception\InvalidFieldNames::class)]
final class InvalidFieldNamesTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNotFoundInReturnsExceptionWhenOnlyOneFieldNameIsProvided(): void
    {
        $faker = self::faker();

        $className = $faker->word();
        $fieldName = $faker->word();

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

        $className = $faker->word();

        /** @var list<string> $fieldNames */
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
