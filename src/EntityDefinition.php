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

use Doctrine\ORM;

/**
 * @internal
 */
final class EntityDefinition
{
    private function __construct(
        private readonly EntityMetadata $entityMetadata,
        private ORM\Mapping\ClassMetadata $doctrineClassMetadata,
        private array $fieldDefinitions,
        private \Closure $afterCreate,
    ) {
    }

    /**
     * @param array<string, FieldDefinition\Resolvable> $fieldDefinitions
     *
     * @throws Exception\InvalidFieldDefinitions
     */
    public static function create(
        EntityMetadata $entityMetadata,
        ORM\Mapping\ClassMetadata $classMetadata,
        array $fieldDefinitions,
        \Closure $afterCreate,
    ): self {
        $invalidFieldDefinitions = \array_filter($fieldDefinitions, static function ($fieldDefinition): bool {
            return !$fieldDefinition instanceof FieldDefinition\Resolvable;
        });

        if ([] !== $invalidFieldDefinitions) {
            throw Exception\InvalidFieldDefinitions::values();
        }

        return new self(
            $entityMetadata,
            $classMetadata,
            $fieldDefinitions,
            $afterCreate,
        );
    }

    public function entityMetadata(): EntityMetadata
    {
        return $this->entityMetadata;
    }

    /**
     * Returns the Doctrine metadata for the entity to be created.
     */
    public function classMetadata(): ORM\Mapping\ClassMetadata
    {
        return $this->doctrineClassMetadata;
    }

    /**
     * @return array<string, FieldDefinition\Resolvable>
     */
    public function fieldDefinitions(): array
    {
        return $this->fieldDefinitions;
    }

    public function afterCreate(): \Closure
    {
        return $this->afterCreate;
    }
}
