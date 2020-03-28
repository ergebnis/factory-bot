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
    private $classMetadata;

    private $fieldDefinitions;

    private $afterCreate;

    /**
     * @param ORM\Mapping\ClassMetadata $classMetadata
     * @param array<string, callable>   $fieldDefinitions
     * @param \Closure                  $afterCreate
     */
    public function __construct(ORM\Mapping\ClassMetadata $classMetadata, array $fieldDefinitions, \Closure $afterCreate)
    {
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
     * Returns the fielde definition callbacks.
     *
     * @return array<string, callable>
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
