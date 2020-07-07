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

namespace Ergebnis\FactoryBot;

final class FieldDefinition
{
    public static function closure(\Closure $closure): FieldDefinition\Closure
    {
        return FieldDefinition\Closure::required($closure);
    }

    public static function optionalClosure(\Closure $closure): FieldDefinition\Closure
    {
        return FieldDefinition\Closure::optional($closure);
    }

    /**
     * @phpstan-param class-string<T> $className
     * @phpstan-return FieldDefinition\Reference<T>
     * @phpstan-template T
     *
     * @psalm-param class-string<T> $className
     * @psalm-return FieldDefinition\Reference<T>
     * @psalm-template T
     *
     * @param string $className
     *
     * @return FieldDefinition\Reference
     */
    public static function reference(string $className): FieldDefinition\Reference
    {
        return FieldDefinition\Reference::required($className);
    }

    /**
     * @phpstan-param class-string<T> $className
     * @phpstan-return FieldDefinition\Reference<T>
     * @phpstan-template T
     *
     * @psalm-param class-string<T> $className
     * @psalm-return FieldDefinition\Reference<T>
     * @psalm-template T
     *
     * @param string $className
     *
     * @return FieldDefinition\Reference
     */
    public static function optionalReference(string $className): FieldDefinition\Reference
    {
        return FieldDefinition\Reference::optional($className);
    }

    /**
     * @phpstan-param class-string<T> $className
     * @phpstan-return FieldDefinition\References<T>
     * @phpstan-template T
     *
     * @psalm-param class-string<T> $className
     * @psalm-return FieldDefinition\References<T>
     * @psalm-template T
     *
     * @param string $className
     * @param int    $count
     *
     * @throws Exception\InvalidCount
     *
     * @return FieldDefinition\References
     */
    public static function references(string $className, int $count = 1): FieldDefinition\References
    {
        return FieldDefinition\References::required(
            $className,
            $count
        );
    }

    /**
     * @phpstan-param class-string<T> $className
     * @phpstan-return FieldDefinition\References<T>
     * @phpstan-template T
     *
     * @psalm-param class-string<T> $className
     * @psalm-return FieldDefinition\References<T>
     * @psalm-template T
     *
     * @param string $className
     * @param int    $count
     *
     * @throws Exception\InvalidCount
     *
     * @return FieldDefinition\References
     */
    public static function optionalReferences(string $className, int $count = 1): FieldDefinition\References
    {
        return FieldDefinition\References::optional(
            $className,
            $count
        );
    }

    /**
     * @param string $value
     * @param int    $initialNumber
     *
     * @throws Exception\InvalidSequence
     *
     * @return FieldDefinition\Sequence
     */
    public static function sequence(string $value, int $initialNumber = 1): FieldDefinition\Sequence
    {
        return FieldDefinition\Sequence::required(
            $value,
            $initialNumber
        );
    }

    /**
     * @param string $value
     * @param int    $initialNumber
     *
     * @return FieldDefinition\Sequence
     */
    public static function optionalSequence(string $value, int $initialNumber = 1): FieldDefinition\Sequence
    {
        return FieldDefinition\Sequence::optional(
            $value,
            $initialNumber
        );
    }

    /**
     * @phpstan-param T $value
     * @phpstan-return FieldDefinition\Value<T>
     * @phpstan-template T
     *
     * @psalm-param T $value
     * @psalm-return FieldDefinition\Value<T>
     * @psalm-template T
     *
     * @param mixed $value
     *
     * @return FieldDefinition\Value
     */
    public static function value($value): FieldDefinition\Value
    {
        return FieldDefinition\Value::required($value);
    }

    /**
     * @phpstan-param T $value
     * @phpstan-return FieldDefinition\Value<T>
     * @phpstan-template T
     *
     * @psalm-param T $value
     * @psalm-return FieldDefinition\Value<T>
     * @psalm-template T
     *
     * @param mixed $value
     *
     * @return FieldDefinition\Value
     */
    public static function optionalValue($value): FieldDefinition\Value
    {
        return FieldDefinition\Value::optional($value);
    }
}
