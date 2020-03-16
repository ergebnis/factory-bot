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
    private $em;

    /**
     * @var array<EntityDef>
     */
    private $entityDefs;

    /**
     * @var array
     */
    private $singletons;

    /**
     * @var bool
     */
    private $persist;

    public function __construct(ORM\EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->entityDefs = [];
        $this->singletons = [];
        $this->persist = false;
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
        if (isset($this->singletons[$name])) {
            return $this->singletons[$name];
        }

        if (!\array_key_exists($name, $this->entityDefs)) {
            throw Exception\EntityDefinitionUnavailable::for($name);
        }

        /** @var EntityDef $def */
        $def = $this->entityDefs[$name];

        $config = $def->getConfig();

        $this->checkFieldOverrides($def, $fieldOverrides);

        /** @var ORM\Mapping\ClassMetadata $entityMetadata */
        $entityMetadata = $def->getEntityMetadata();

        $ent = $entityMetadata->newInstance();

        $fieldValues = [];

        foreach ($def->getFieldDefs() as $fieldName => $fieldDef) {
            $fieldValues[$fieldName] = \array_key_exists($fieldName, $fieldOverrides)
                ? $fieldOverrides[$fieldName]
                : $fieldDef($this);
        }

        foreach ($fieldValues as $fieldName => $fieldValue) {
            $this->setField($ent, $def, $fieldName, $fieldValue);
        }

        if (isset($config['afterCreate'])) {
            $config['afterCreate']($ent, $fieldValues);
        }

        if ($this->persist && false === $entityMetadata->isEmbeddedClass) {
            $this->em->persist($ent);
        }

        return $ent;
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
        if (isset($this->singletons[$name])) {
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
     * Defines how to create a default entity of type `$name`.
     *
     * See the readme for a tutorial.
     *
     * @param mixed $name
     * @param array $fieldDefs
     * @param array $config
     *
     * @return FixtureFactory
     */
    public function defineEntity($name, array $fieldDefs = [], array $config = [])
    {
        if (isset($this->entityDefs[$name])) {
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

        $metadata = $this->em->getClassMetadata($type);

        if (!isset($metadata)) {
            throw new \Exception(\sprintf(
                'Unknown entity type: %s',
                $type
            ));
        }

        $this->entityDefs[$name] = new EntityDef(
            $metadata,
            $type,
            $fieldDefs,
            $config
        );

        return $this;
    }

    /**
     * @return EntityDef[]
     */
    public function definitions(): array
    {
        return $this->entityDefs;
    }

    private function checkFieldOverrides(EntityDef $def, array $fieldOverrides): void
    {
        $extraFields = \array_diff(\array_keys($fieldOverrides), \array_keys($def->getFieldDefs()));

        if (!empty($extraFields)) {
            throw new \Exception(\sprintf(
                'Field(s) not in %s: \'%s\'',
                $def->getEntityType(),
                \implode("', '", $extraFields)
            ));
        }
    }

    private function setField($ent, EntityDef $def, $fieldName, $fieldValue): void
    {
        $metadata = $def->getEntityMetadata();

        if ($metadata->isCollectionValuedAssociation($fieldName)) {
            $metadata->setFieldValue($ent, $fieldName, $this->createCollectionFrom($fieldValue));
        } else {
            $metadata->setFieldValue($ent, $fieldName, $fieldValue);

            if (\is_object($fieldValue) && $metadata->isSingleValuedAssociation($fieldName)) {
                $this->updateCollectionSideOfAssocation($ent, $metadata, $fieldName, $fieldValue);
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

    private function updateCollectionSideOfAssocation($entityBeingCreated, $metadata, $fieldName, $value): void
    {
        $assoc = $metadata->getAssociationMapping($fieldName);

        $inverse = $assoc['inversedBy'];

        if ($inverse) {
            $valueMetadata = $this->em->getClassMetadata(\get_class($value));
            $collection = $valueMetadata->getFieldValue($value, $inverse);

            if ($collection instanceof Common\Collections\Collection) {
                $collection->add($entityBeingCreated);
            }
        }
    }
}
