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

use Doctrine\ORM;

/**
 * @internal
 */
final class EntityDefinition
{
    /**
     * @var ORM\Mapping\ClassMetadata
     */
    private $classMetadata;

    /**
     * @var array<string, FieldDefinition\Resolvable>
     */
    private $fieldDefinitions;

    /**
     * @var \Closure
     */
    private $afterCreate;

    /**
     * @param ORM\Mapping\ClassMetadata                 $classMetadata
     * @param array<string, FieldDefinition\Resolvable> $fieldDefinitions
     * @param \Closure                                  $afterCreate
     *
     * @throws Exception\InvalidFieldDefinitions
     */
    public function __construct(ORM\Mapping\ClassMetadata $classMetadata, array $fieldDefinitions, \Closure $afterCreate)
    {
        $invalidFieldDefinitions = \array_filter($fieldDefinitions, static function ($fieldDefinition): bool {
            return !$fieldDefinition instanceof FieldDefinition\Resolvable;
        });

        if ([] !== $invalidFieldDefinitions) {
            throw Exception\InvalidFieldDefinitions::values();
        }

        $this->classMetadata = $classMetadata;
        $this->fieldDefinitions = $fieldDefinitions;
        $this->afterCreate = $afterCreate;
    }

    /**
     * Returns the Doctrine metadata for the entity to be created.
     *
     * @return ORM\Mapping\ClassMetadata
     */
    public function classMetadata(): ORM\Mapping\ClassMetadata
    {
        return $this->classMetadata;
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
