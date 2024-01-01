<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot;

final class FieldDefinition
{
    public static function closure(\Closure $closure): FieldDefinition\Resolvable
    {
        return new FieldDefinition\Closure($closure);
    }

    public static function optionalClosure(\Closure $closure): FieldDefinition\Optional
    {
        return new FieldDefinition\Optional(new FieldDefinition\Closure($closure));
    }

    /**
     * @phpstan-param class-string<T> $className
     * @phpstan-param array<string, \Closure|mixed|FieldDefinition\Resolvable> $fieldDefinitionOverrides
     *
     * @phpstan-return FieldDefinition\Reference<T>
     *
     * @phpstan-template T
     *
     * @psalm-param class-string<T> $className
     * @psalm-param array<string, \Closure|mixed|FieldDefinition\Resolvable> $fieldDefinitionOverrides
     *
     * @psalm-return FieldDefinition\Reference<T>
     *
     * @psalm-template T
     *
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitionOverrides
     */
    public static function reference(
        string $className,
        array $fieldDefinitionOverrides = [],
    ): FieldDefinition\Reference {
        return new FieldDefinition\Reference(
            $className,
            $fieldDefinitionOverrides,
        );
    }

    /**
     * @phpstan-param class-string $className
     * @phpstan-param array<string, \Closure|mixed|FieldDefinition\Resolvable> $fieldDefinitionOverrides
     *
     * @psalm-param class-string $className
     * @psalm-param array<string, \Closure|mixed|FieldDefinition\Resolvable> $fieldDefinitionOverrides
     *
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitionOverrides
     */
    public static function optionalReference(
        string $className,
        array $fieldDefinitionOverrides = [],
    ): FieldDefinition\Optional {
        return new FieldDefinition\Optional(new FieldDefinition\Reference(
            $className,
            $fieldDefinitionOverrides,
        ));
    }

    /**
     * @phpstan-param class-string<T> $className
     *
     * @phpstan-return FieldDefinition\References<T>
     *
     * @phpstan-param array<string, \Closure|mixed|FieldDefinition\Resolvable> $fieldDefinitionOverrides
     *
     * @phpstan-template T
     *
     * @psalm-param class-string<T> $className
     * @psalm-param array<string, \Closure|mixed|FieldDefinition\Resolvable> $fieldDefinitionOverrides
     *
     * @psalm-return FieldDefinition\References<T>
     *
     * @psalm-template T
     *
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitionOverrides
     */
    public static function references(
        string $className,
        Count $count,
        array $fieldDefinitionOverrides = [],
    ): FieldDefinition\References {
        return new FieldDefinition\References(
            $className,
            $count,
            $fieldDefinitionOverrides,
        );
    }

    /**
     * @throws Exception\InvalidSequence
     */
    public static function sequence(
        string $value,
        int $initialNumber = 1,
    ): FieldDefinition\Sequence {
        return new FieldDefinition\Sequence(
            $value,
            $initialNumber,
        );
    }

    public static function optionalSequence(
        string $value,
        int $initialNumber = 1,
    ): FieldDefinition\Resolvable {
        return new FieldDefinition\Optional(new FieldDefinition\Sequence(
            $value,
            $initialNumber,
        ));
    }

    /**
     * @phpstan-param T $value
     *
     * @phpstan-return FieldDefinition\Value<T>
     *
     * @phpstan-template T
     *
     * @psalm-param T $value
     *
     * @psalm-return FieldDefinition\Value<T>
     *
     * @psalm-template T
     */
    public static function value(mixed $value): FieldDefinition\Value
    {
        return new FieldDefinition\Value($value);
    }

    public static function optionalValue(mixed $value): FieldDefinition\Optional
    {
        return new FieldDefinition\Optional(new FieldDefinition\Value($value));
    }
}
