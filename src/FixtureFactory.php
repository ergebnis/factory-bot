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

use Doctrine\Common;
use Doctrine\ORM;

/**
 * Creates Doctrine entities for use in tests.
 *
 * See the README file for a tutorial.
 */
final class FixtureFactory
{
    private $entityManager;

    /**
     * @var array<string, EntityDefinition>
     */
    private $entityDefinitions = [];

    /**
     * @var array<string, object>
     */
    private $singletons = [];

    /**
     * @var bool
     */
    private $persist = false;

    public function __construct(ORM\EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Defines how to create a default entity of type `$className`.
     *
     * See the readme for a tutorial.
     *
     * @param string   $className
     * @param array    $fieldDefinitions
     * @param \Closure $afterCreate
     *
     * @throws Exception\InvalidFieldNames
     * @throws \Exception
     *
     * @return FixtureFactory
     */
    public function defineEntity(string $className, array $fieldDefinitions = [], ?\Closure $afterCreate = null)
    {
        if (\array_key_exists($className, $this->entityDefinitions)) {
            throw new \Exception(\sprintf(
                "Entity '%s' already defined in fixture factory",
                $className
            ));
        }

        if (!\class_exists($className, true)) {
            throw new \Exception(\sprintf(
                'Not a class: %s',
                $className
            ));
        }

        /** @var null|ORM\Mapping\ClassMetadata $classMetadata */
        $classMetadata = $this->entityManager->getClassMetadata($className);

        if (null === $classMetadata) {
            throw new \Exception(\sprintf(
                'Unknown entity type: %s',
                $className
            ));
        }

        /** @var string[] $allFieldNames */
        $allFieldNames = \array_merge(
            \array_keys($classMetadata->fieldMappings),
            \array_keys($classMetadata->associationMappings),
            \array_keys($classMetadata->embeddedClasses)
        );

        $fieldNames = \array_filter($allFieldNames, static function (string $fieldName): bool {
            return false === \strpos($fieldName, '.');
        });

        /** @var array<int, string> $extraFieldNames */
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

        $fieldDefinitions = \array_map(static function ($fieldDefinition): callable {
            return self::normalizeFieldDefinition($fieldDefinition);
        }, $fieldDefinitions);

        $defaultEntity = $classMetadata->newInstance();

        foreach ($fieldNames as $fieldName) {
            if (\array_key_exists($fieldName, $fieldDefinitions)) {
                continue;
            }

            $defaultFieldValue = $classMetadata->getFieldValue($defaultEntity, $fieldName);

            if (null === $defaultFieldValue) {
                $fieldDefinitions[$fieldName] = static function () {
                    return null;
                };

                continue;
            }

            $fieldDefinitions[$fieldName] = static function () use ($defaultFieldValue) {
                return $defaultFieldValue;
            };
        }

        if (null === $afterCreate) {
            $afterCreate = static function (): void {
                // nothing to do here
            };
        }

        $this->entityDefinitions[$className] = new EntityDefinition(
            $classMetadata,
            $fieldDefinitions,
            $afterCreate
        );

        return $this;
    }

    /**
     * Get an entity and its dependencies.
     *
     * Whether the entity is new or not depends on whether you've created
     * a singleton with the entity name. See `getAsSingleton()`.
     *
     * If you've called `persistOnGet()` then the entity is also persisted.
     *
     * @param string               $className
     * @param array<string, mixed> $fieldOverrides
     *
     * @throws Exception\EntityDefinitionUnavailable
     * @throws Exception\InvalidFieldNames
     */
    public function get(string $className, array $fieldOverrides = []): object
    {
        if (\array_key_exists($className, $this->singletons)) {
            return $this->singletons[$className];
        }

        if (!\array_key_exists($className, $this->entityDefinitions)) {
            throw Exception\EntityDefinitionUnavailable::for($className);
        }

        /** @var EntityDefinition $entityDefinition */
        $entityDefinition = $this->entityDefinitions[$className];

        $extraFieldNames = \array_diff(
            \array_keys($fieldOverrides),
            \array_keys($entityDefinition->fieldDefinitions())
        );

        if ([] !== $extraFieldNames) {
            throw Exception\InvalidFieldNames::notFoundIn(
                $entityDefinition->classMetadata()->getName(),
                ...$extraFieldNames
            );
        }

        /** @var ORM\Mapping\ClassMetadata $classMetadata */
        $classMetadata = $entityDefinition->classMetadata();

        $entity = $classMetadata->newInstance();

        $fieldValues = [];

        foreach ($entityDefinition->fieldDefinitions() as $fieldName => $fieldDefinition) {
            $fieldValues[$fieldName] = \array_key_exists($fieldName, $fieldOverrides)
                ? $fieldOverrides[$fieldName]
                : $fieldDefinition($this);
        }

        foreach ($fieldValues as $fieldName => $fieldValue) {
            $this->setField($entity, $entityDefinition, $fieldName, $fieldValue);
        }

        $afterCreate = $entityDefinition->afterCreate();

        $afterCreate(
            $entity,
            $fieldValues
        );

        if ($this->persist && false === $classMetadata->isEmbeddedClass) {
            $this->entityManager->persist($entity);
        }

        return $entity;
    }

    /**
     * Get an array of entities and their dependencies.
     *
     * Whether the entities are new or not depends on whether you've created
     * a singleton with the entity name. See `getAsSingleton()`.
     *
     * If you've called `persistOnGet()` then the entities are also persisted.
     *
     * @param string $className
     * @param array  $fieldOverrides
     * @param int    $numberOfInstances
     *
     * @throws \InvalidArgumentException
     *
     * @return object[]
     */
    public function getList(string $className, array $fieldOverrides = [], int $numberOfInstances = 1): array
    {
        if (1 > $numberOfInstances) {
            throw new \InvalidArgumentException('Can only get >= 1 instances');
        }

        if (1 < $numberOfInstances && \array_key_exists($className, $this->singletons)) {
            $numberOfInstances = 1;
        }

        $instances = [];

        for ($i = 0; $i < $numberOfInstances; ++$i) {
            $instances[] = $this->get($className, $fieldOverrides);
        }

        return $instances;
    }

    /**
     * Sets whether `get()` should automatically persist the entity it creates.
     * By default it does not. In any case, you still need to call
     * flush() yourself.
     *
     * @param bool $enabled
     */
    public function persistOnGet(bool $enabled = true): void
    {
        $this->persist = $enabled;
    }

    /**
     * A shorthand combining `get()` and `setSingleton()`.
     *
     * It's illegal to call this if `$className` already has a singleton.
     *
     * @param string               $className
     * @param array<string, mixed> $fieldOverrides
     *
     * @throws \Exception
     *
     * @return object
     */
    public function getAsSingleton(string $className, array $fieldOverrides = []): object
    {
        if (\array_key_exists($className, $this->singletons)) {
            throw new \Exception(\sprintf(
                'Already a singleton: %s',
                $className
            ));
        }

        $this->singletons[$className] = $this->get($className, $fieldOverrides);

        return $this->singletons[$className];
    }

    /**
     * Sets `$entity` to be the singleton for `$className`.
     *
     * This causes `get($name)` to return `$entity`.
     *
     * @param string $className
     * @param object $entity
     */
    public function setSingleton(string $className, object $entity): void
    {
        $this->singletons[$className] = $entity;
    }

    /**
     * Unsets the singleton for `$className`.
     *
     * This causes `get($className)` to return new entities again.
     *
     * @param string $className
     */
    public function unsetSingleton(string $className): void
    {
        unset($this->singletons[$className]);
    }

    /**
     * @return EntityDefinition[]
     */
    public function definitions(): array
    {
        return $this->entityDefinitions;
    }

    private function setField(object $entity, EntityDefinition $entityDefinition, string $fieldName, $fieldValue): void
    {
        $classMetadata = $entityDefinition->classMetadata();

        if ($classMetadata->isCollectionValuedAssociation($fieldName)) {
            $classMetadata->setFieldValue($entity, $fieldName, self::createCollectionFrom($fieldValue));
        } else {
            $classMetadata->setFieldValue($entity, $fieldName, $fieldValue);

            if (\is_object($fieldValue) && $classMetadata->isSingleValuedAssociation($fieldName)) {
                $this->updateCollectionSideOfAssocation($entity, $classMetadata, $fieldName, $fieldValue);
            }
        }
    }

    private static function createCollectionFrom($array = []): Common\Collections\ArrayCollection
    {
        if (\is_array($array)) {
            return new Common\Collections\ArrayCollection($array);
        }

        return new Common\Collections\ArrayCollection();
    }

    private function updateCollectionSideOfAssocation(
        object $entity,
        ORM\Mapping\ClassMetadata $classMetadata,
        string $fieldName,
        $fieldValue
    ): void {
        $association = $classMetadata->getAssociationMapping($fieldName);

        $inversedBy = $association['inversedBy'];

        if ($inversedBy) {
            $classMetadataOfFieldValue = $this->entityManager->getClassMetadata(\get_class($fieldValue));
            $collection = $classMetadataOfFieldValue->getFieldValue($fieldValue, $inversedBy);

            if ($collection instanceof Common\Collections\Collection) {
                $collection->add($entity);
            }
        }
    }

    /**
     * @param callable|\Closure|mixed|object $fieldDefinition
     *
     * @return callable
     */
    private static function normalizeFieldDefinition($fieldDefinition): callable
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
