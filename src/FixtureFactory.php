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
    /**
     * @var ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array<EntityDef>
     */
    private $entityDefinitions;

    /**
     * @var array
     */
    private $singletons;

    /**
     * @var bool
     */
    private $persist;

    public function __construct(ORM\EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityDefinitions = [];
        $this->singletons = [];
        $this->persist = false;
    }

    /**
     * Defines how to create a default entity of type `$name`.
     *
     * See the readme for a tutorial.
     *
     * @param mixed $name
     * @param array $fieldDefinitions
     * @param array $configuration
     *
     * @return FixtureFactory
     */
    public function defineEntity($name, array $fieldDefinitions = [], array $configuration = [])
    {
        if (\array_key_exists($name, $this->entityDefinitions)) {
            throw new \Exception(\sprintf(
                "Entity '%s' already defined in fixture factory",
                $name
            ));
        }

        $type = $name;

        if (!\class_exists($type, true)) {
            throw new \Exception(\sprintf(
                'Not a class: %s',
                $type
            ));
        }

        /** @var null|ORM\Mapping\ClassMetadata $classMetadata */
        $classMetadata = $this->entityManager->getClassMetadata($type);

        if (null === $classMetadata) {
            throw new \Exception(\sprintf(
                'Unknown entity type: %s',
                $type
            ));
        }

        $this->entityDefinitions[$name] = new EntityDef(
            $classMetadata,
            $fieldDefinitions,
            $configuration
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
     * @param mixed $name
     * @param array $fieldOverrides
     *
     * @throws Exception\EntityDefinitionUnavailable
     */
    public function get($name, array $fieldOverrides = [])
    {
        if (\array_key_exists($name, $this->singletons)) {
            return $this->singletons[$name];
        }

        if (!\array_key_exists($name, $this->entityDefinitions)) {
            throw Exception\EntityDefinitionUnavailable::for($name);
        }

        /** @var EntityDef $entityDefinition */
        $entityDefinition = $this->entityDefinitions[$name];

        $configuration = $entityDefinition->getConfiguration();

        $this->checkFieldOverrides($entityDefinition, $fieldOverrides);

        /** @var ORM\Mapping\ClassMetadata $classMetadata */
        $classMetadata = $entityDefinition->getClassMetadata();

        $entity = $classMetadata->newInstance();

        $fieldValues = [];

        foreach ($entityDefinition->getFieldDefinitions() as $fieldName => $fieldDefinition) {
            $fieldValues[$fieldName] = \array_key_exists($fieldName, $fieldOverrides)
                ? $fieldOverrides[$fieldName]
                : $fieldDefinition($this);
        }

        foreach ($fieldValues as $fieldName => $fieldValue) {
            $this->setField($entity, $entityDefinition, $fieldName, $fieldValue);
        }

        if (\array_key_exists('afterCreate', $configuration)) {
            $configuration['afterCreate']($entity, $fieldValues);
        }

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
     * @param mixed $name
     * @param array $fieldOverrides
     * @param mixed $numberOfInstances
     */
    public function getList($name, array $fieldOverrides = [], $numberOfInstances = 1)
    {
        if (1 > $numberOfInstances) {
            throw new \InvalidArgumentException('Can only get >= 1 instances');
        }

        if (1 < $numberOfInstances && \array_key_exists($name, $this->singletons)) {
            $numberOfInstances = 1;
        }

        $instances = [];

        for ($i = 0; $i < $numberOfInstances; ++$i) {
            $instances[] = $this->get($name, $fieldOverrides);
        }

        return $instances;
    }

    /**
     * Sets whether `get()` should automatically persist the entity it creates.
     * By default it does not. In any case, you still need to call
     * flush() yourself.
     *
     * @param mixed $enabled
     */
    public function persistOnGet($enabled = true): void
    {
        $this->persist = $enabled;
    }

    /**
     * A shorthand combining `get()` and `setSingleton()`.
     *
     * It's illegal to call this if `$name` already has a singleton.
     *
     * @param mixed $name
     * @param array $fieldOverrides
     */
    public function getAsSingleton($name, array $fieldOverrides = [])
    {
        if (\array_key_exists($name, $this->singletons)) {
            throw new \Exception(\sprintf(
                'Already a singleton: %s',
                $name
            ));
        }

        $this->singletons[$name] = $this->get($name, $fieldOverrides);

        return $this->singletons[$name];
    }

    /**
     * Sets `$entity` to be the singleton for `$name`.
     *
     * This causes `get($name)` to return `$entity`.
     *
     * @param mixed $name
     * @param mixed $entity
     */
    public function setSingleton($name, $entity): void
    {
        $this->singletons[$name] = $entity;
    }

    /**
     * Unsets the singleton for `$name`.
     *
     * This causes `get($name)` to return new entities again.
     *
     * @param mixed $name
     */
    public function unsetSingleton($name): void
    {
        unset($this->singletons[$name]);
    }

    /**
     * @return EntityDef[]
     */
    public function definitions(): array
    {
        return $this->entityDefinitions;
    }

    private function checkFieldOverrides(EntityDef $entityDefinition, array $fieldOverrides): void
    {
        $extraFields = \array_diff(\array_keys($fieldOverrides), \array_keys($entityDefinition->getFieldDefinitions()));

        if (!empty($extraFields)) {
            throw new \Exception(\sprintf(
                'Field(s) not in %s: \'%s\'',
                $entityDefinition->getClassName(),
                \implode("', '", $extraFields)
            ));
        }
    }

    private function setField($entity, EntityDef $entityDefinition, $fieldName, $fieldValue): void
    {
        $classMetadata = $entityDefinition->getClassMetadata();

        if ($classMetadata->isCollectionValuedAssociation($fieldName)) {
            $classMetadata->setFieldValue($entity, $fieldName, $this->createCollectionFrom($fieldValue));
        } else {
            $classMetadata->setFieldValue($entity, $fieldName, $fieldValue);

            if (\is_object($fieldValue) && $classMetadata->isSingleValuedAssociation($fieldName)) {
                $this->updateCollectionSideOfAssocation($entity, $classMetadata, $fieldName, $fieldValue);
            }
        }
    }

    private function createCollectionFrom($array = [])
    {
        if (\is_array($array)) {
            return new Common\Collections\ArrayCollection($array);
        }

        return new Common\Collections\ArrayCollection();
    }

    private function updateCollectionSideOfAssocation($entity, $classMetadata, $fieldName, $fieldValue): void
    {
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
}
