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

/**
 * Contains static methods to define fields as sequences, references etc.
 */
final class FieldDefinition
{
    /**
     * Defines a field to be a string based on an incrementing integer.
     *
     * This is typically used to generate unique names such as usernames.
     *
     * The parameter may be a function that receives a counter value
     * each time the entity is created or it may be a string.
     *
     * If the parameter is a string string containing "%d" then it will be
     * replaced by the counter value. If the string does not contain "%d"
     * then the number is simply appended to the parameter.
     *
     * @param callable|string $funcOrString the function or pattern to generate a value from
     * @param int             $firstNum     the first number to use
     *
     * @return \Closure
     */
    public static function sequence($funcOrString, int $firstNum = 1): \Closure
    {
        $n = $firstNum - 1;

        if (\is_callable($funcOrString)) {
            return static function () use (&$n, $funcOrString) {
                ++$n;

                return \call_user_func($funcOrString, $n);
            };
        }

        if (false !== \strpos($funcOrString, '%d')) {
            return static function () use (&$n, $funcOrString) {
                ++$n;

                return \str_replace('%d', $n, $funcOrString);
            };
        }

        return static function () use (&$n, $funcOrString) {
            ++$n;

            return $funcOrString . $n;
        };
    }

    /**
     * Defines a field to `get()` a named entity from the factory.
     *
     * The normal semantics of `get()` apply.
     * Normally this means that the field gets a fresh instance of the named
     * entity. If a singleton has been defined, `get()` will return that.
     *
     * @param string $className
     *
     * @return \Closure
     */
    public static function reference(string $className): \Closure
    {
        return static function (FixtureFactory $fixtureFactory) use ($className): object {
            return $fixtureFactory->get($className);
        };
    }

    /**
     * Defines a field to `get()` a collection of named entities from the factory.
     *
     * The normal semantics of `get()` apply.
     *
     * Normally this means that the field gets a fresh instance of the named
     * entity. If a singleton has been defined, a collection with a single instance will be returned.
     *
     * @param string $className
     * @param int    $count
     *
     * @throws Exception\InvalidCount
     *
     * @return \Closure
     */
    public static function references(string $className, int $count = 1): \Closure
    {
        $minimumCount = 1;

        if ($minimumCount > $count) {
            throw Exception\InvalidCount::notGreaterThanorEqualTo(
                $minimumCount,
                $count
            );
        }

        return static function (FixtureFactory $fixtureFactory) use ($className, $count): array {
            return $fixtureFactory->getList(
                $className,
                [],
                $count
            );
        };
    }
}
