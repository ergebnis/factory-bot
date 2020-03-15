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
    private $name;

    private $entityType;

    /**
     * @var ORM\Mapping\ClassMetadata
     */
    private $metadata;

    private $fieldDefs;

    private $config;

    public function __construct(ORM\EntityManagerInterface $em, $name, $type, array $fieldDefs, array $config)
    {
        $this->name = $name;
        $this->entityType = $type;
        $this->metadata = $em->getClassMetadata($type);
        $this->fieldDefs = [];
        $this->config = $config;

        $this->readFieldDefs($fieldDefs);
        $this->defaultDefsFromMetadata();
    }

    /**
     * Returns the name of the entity definition.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the fully qualified name of the entity class.
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Returns the fielde definition callbacks.
     */
    public function getFieldDefs()
    {
        return $this->fieldDefs;
    }

    /**
     * Returns the Doctrine metadata for the entity to be created.
     *
     * @return ORM\Mapping\ClassMetadata
     */
    public function getEntityMetadata()
    {
        return $this->metadata;
    }

    /**
     * Returns the extra configuration array of the entity definition.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    private function readFieldDefs(array $params): void
    {
        foreach ($params as $key => $def) {
            if ($this->metadata->hasField($key) ||
                $this->metadata->hasAssociation($key)) {
                $this->fieldDefs[$key] = $this->normalizeFieldDef($def);
            } else {
                throw new \Exception('No such field in ' . $this->entityType . ': ' . $key);
            }
        }
    }

    private function defaultDefsFromMetadata(): void
    {
        $defaultEntity = $this->getEntityMetadata()->newInstance();

        $allFields = \array_merge($this->metadata->getFieldNames(), $this->metadata->getAssociationNames());

        foreach ($allFields as $fieldName) {
            if (!isset($this->fieldDefs[$fieldName])) {
                $defaultFieldValue = $this->metadata->getFieldValue($defaultEntity, $fieldName);

                if (null !== $defaultFieldValue) {
                    $this->fieldDefs[$fieldName] = static function () use ($defaultFieldValue) {
                        return $defaultFieldValue;
                    };
                } else {
                    $this->fieldDefs[$fieldName] = static function () {
                        return null;
                    };
                }
            }
        }
    }

    private function normalizeFieldDef($def)
    {
        if (\is_callable($def)) {
            return $this->ensureInvokable($def);
        }

        return static function () use ($def) {
            return $def;
        };
    }

    private function ensureInvokable($f)
    {
        if (\method_exists($f, '__invoke')) {
            return $f;
        }

        return static function () use ($f) {
            return \call_user_func_array($f, \func_get_args());
        };
    }
}
