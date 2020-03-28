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
final class EntityDef
{
    /**
     * @var ORM\Mapping\ClassMetadata
     */
    private $classMetadata;

    private $fieldDefinitions;

    private $configuration;

    /**
     * @param ORM\Mapping\ClassMetadata $classMetadata
     * @param array                     $fieldDefinitions
     * @param array                     $configuration
     *
     * @throws Exception\InvalidFieldNames
     * @throws \Exception
     */
    public function __construct(ORM\Mapping\ClassMetadata $classMetadata, array $fieldDefinitions, array $configuration)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldDefinitions = [];
        $this->configuration = $configuration;

        /** @var string[] $allFieldNames */
        $allFieldNames = \array_merge(
            \array_keys($this->classMetadata->fieldMappings),
            \array_keys($this->classMetadata->associationMappings),
            \array_keys($this->classMetadata->embeddedClasses)
        );

        $fieldNames = \array_filter($allFieldNames, static function (string $fieldName): bool {
            return false === \strpos($fieldName, '.');
        });

        $extraFieldNames = \array_diff(
            \array_keys($fieldDefinitions),
            $fieldNames
        );

        if ([] !== $extraFieldNames) {
            throw Exception\InvalidFieldNames::notFoundIn(
                $classMetadata->getName(),
                ...$extraFieldNames
            );
        }

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $this->fieldDefinitions[$fieldName] = $this->normalizeFieldDefinition($fieldDefinition);
        }

        $defaultEntity = $this->classMetadata->newInstance();

        foreach ($fieldNames as $fieldName) {
            if (\array_key_exists($fieldName, $this->fieldDefinitions)) {
                continue;
            }

            $defaultFieldValue = $this->classMetadata->getFieldValue($defaultEntity, $fieldName);

            if (null === $defaultFieldValue) {
                $this->fieldDefinitions[$fieldName] = static function () {
                    return null;
                };

                continue;
            }

            $this->fieldDefinitions[$fieldName] = static function () use ($defaultFieldValue) {
                return $defaultFieldValue;
            };
        }
    }

    /**
     * Returns the fully qualified name of the entity class.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->classMetadata->getName();
    }

    /**
     * Returns the fielde definition callbacks.
     */
    public function getFieldDefinitions()
    {
        return $this->fieldDefinitions;
    }

    /**
     * Returns the Doctrine metadata for the entity to be created.
     *
     * @return ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->classMetadata;
    }

    /**
     * Returns the extra configuration array of the entity definition.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    private function normalizeFieldDefinition($fieldDefinition)
    {
        if (\is_callable($fieldDefinition)) {
            if (\method_exists($fieldDefinition, '__invoke')) {
                return $fieldDefinition;
            }

            return static function () use ($fieldDefinition) {
                return \call_user_func_array($fieldDefinition, \func_get_args());
            };
        }

        return static function () use ($fieldDefinition) {
            return $fieldDefinition;
        };
    }
}
