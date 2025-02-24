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
     * @template T of object
     *
     * @param class-string<T>                                          $className
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitionOverrides
     *
     * @return FieldDefinition\Reference<T>
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
     * @template T of object
     *
     * @param class-string<T>                                          $className
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
     * @template T of object
     *
     * @param class-string<T>                                          $className
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitionOverrides
     *
     * @return FieldDefinition\References<T>
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
     * @template T of mixed
     *
     * @param T $value
     *
     * @return FieldDefinition\Value<T>
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
