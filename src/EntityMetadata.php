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

/**
 * @internal
 */
final class EntityMetadata
{
    /**
     * @param class-string $className
     * @param list<string> $fieldNames
     */
    private function __construct(
        private readonly string $className,
        private readonly array $fieldNames,
    ) {
    }

    /**
     * @param class-string $className
     * @param list<string> $fieldNames
     */
    public static function create(
        string $className,
        array $fieldNames,
    ): self {
        return new self(
            $className,
            $fieldNames,
        );
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }

    /**
     * @return list<string>
     */
    public function fieldNames(): array
    {
        return $this->fieldNames;
    }
}
