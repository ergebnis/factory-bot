<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot;

use Doctrine\Common;
use Doctrine\Instantiator;
use Doctrine\ORM;
use Ergebnis\Classy;
use Faker\Generator;

final class FixtureFactory
{
    private Instantiator\Instantiator $instantiator;
    private FieldResolution\FieldValueResolution\FieldValueResolutionStrategy $fieldValueResolutionStrategy;
    private FieldResolution\CountResolution\CountResolutionStrategy $countResolutionStrategy;
    private bool $persistAfterCreate = false;

    /**
     * @var array<string, EntityDefinition>
     */
    private array $entityDefinitions = [];

    public function __construct(
        private ORM\EntityManagerInterface $entityManager,
        private Generator $faker,
    ) {
        $this->instantiator = new Instantiator\Instantiator();
        $this->fieldValueResolutionStrategy = new FieldResolution\FieldValueResolution\WithOrWithoutOptionalFieldValue();
        $this->countResolutionStrategy = new FieldResolution\CountResolution\BetweenMinimumAndMaximumCount();
    }

    /**
     * Creates a definition for populating an entity with fake data.
     *
     * @template T of object
     *
     * @param class-string<T>                                          $className
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitions
     *
     * @throws Exception\ClassMetadataNotFound
     * @throws Exception\ClassNotFound
     * @throws Exception\EntityDefinitionAlreadyRegistered
     * @throws Exception\InvalidFieldNames
     */
    public function define(
        string $className,
        array $fieldDefinitions = [],
        ?\Closure $afterCreate = null,
    ): void {
        if (\array_key_exists($className, $this->entityDefinitions)) {
            throw Exception\EntityDefinitionAlreadyRegistered::for($className);
        }

        if (!\class_exists($className, true)) {
            throw Exception\ClassNotFound::name($className);
        }

        try {
            $classMetadata = $this->entityManager->getClassMetadata($className);
        } catch (ORM\Mapping\MappingException) {
            throw Exception\ClassMetadataNotFound::for($className);
        }

        /** @var array<int, string> $allFieldNames */
        $allFieldNames = \array_merge(
            \array_keys($classMetadata->fieldMappings),
            \array_keys($classMetadata->associationMappings),
            \array_keys($classMetadata->embeddedClasses),
        );

        $fieldNames = \array_filter($allFieldNames, static function (string $fieldName): bool {
            return !\str_contains($fieldName, '.');
        });

        /** @var array<int, string> $extraFieldNames */
        $extraFieldNames = \array_diff(
            \array_keys($fieldDefinitions),
            $fieldNames,
        );

        if ([] !== $extraFieldNames) {
            throw Exception\InvalidFieldNames::notFoundIn(
                $classMetadata->getName(),
                ...$extraFieldNames,
            );
        }

        $fieldDefinitions = self::normalizeFieldDefinitions($fieldDefinitions);

        $defaultEntity = $this->instantiator->instantiate($className);

        foreach ($fieldNames as $fieldName) {
            if (\array_key_exists($fieldName, $fieldDefinitions)) {
                continue;
            }

            /** @var mixed $defaultFieldValue */
            $defaultFieldValue = $classMetadata->getFieldValue(
                $defaultEntity,
                $fieldName,
            );

            $fieldDefinitions[$fieldName] = FieldDefinition::value($defaultFieldValue);
        }

        if (null === $afterCreate) {
            $afterCreate = static function (): void {
                // nothing to do here
            };
        }

        $this->entityDefinitions[$className] = EntityDefinition::create(
            $classMetadata,
            $fieldDefinitions,
            $afterCreate,
        );
    }

    /**
     * Loads entity-definition providers from the specified directory.
     *
     * @throws Exception\InvalidDefinition
     * @throws Exception\InvalidDirectory
     */
    public function load(string $directory): void
    {
        if (!\is_dir($directory)) {
            throw Exception\InvalidDirectory::notDirectory($directory);
        }

        foreach (Classy\Constructs::fromDirectory($directory) as $construct) {
            /** @var class-string $className */
            $className = $construct->name();

            try {
                $reflection = new \ReflectionClass($className);
            } catch (\ReflectionException) {
                throw Exception\InvalidDefinition::canNotBeAutoloaded($className);
            }

            if (!$reflection->implementsInterface(EntityDefinitionProvider::class)) {
                continue;
            }

            if ($reflection->isAbstract()) {
                continue;
            }

            if (!$reflection->isInstantiable()) {
                throw Exception\InvalidDefinition::canNotBeInstantiated($className);
            }

            try {
                /** @var EntityDefinitionProvider $provider */
                $provider = $reflection->newInstance();
            } catch (\Exception $exception) {
                throw Exception\InvalidDefinition::throwsExceptionDuringInstantiation(
                    $className,
                    $exception,
                );
            }

            $provider->accept($this);
        }
    }

    public function register(EntityDefinitionProvider ...$providers): void
    {
        foreach ($providers as $provider) {
            $provider->accept($this);
        }
    }

    /**
     * Creates a single entity with all of its dependencies.
     *
     * @template T of object
     *
     * @param class-string<T>                                          $className
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitionOverrides
     *
     * @throws Exception\EntityDefinitionNotRegistered
     * @throws Exception\InvalidFieldNames
     *
     * @return T
     */
    public function createOne(
        string $className,
        array $fieldDefinitionOverrides = [],
    ) {
        if (!\array_key_exists($className, $this->entityDefinitions)) {
            throw Exception\EntityDefinitionNotRegistered::for($className);
        }

        /** @var EntityDefinition $entityDefinition */
        $entityDefinition = $this->entityDefinitions[$className];

        $extraFieldNames = \array_diff(
            \array_keys($fieldDefinitionOverrides),
            \array_keys($entityDefinition->fieldDefinitions()),
        );

        if ([] !== $extraFieldNames) {
            throw Exception\InvalidFieldNames::notFoundIn(
                $entityDefinition->classMetadata()->getName(),
                ...$extraFieldNames,
            );
        }

        /** @var ORM\Mapping\ClassMetadata $classMetadata */
        $classMetadata = $entityDefinition->classMetadata();

        $entity = $this->instantiator->instantiate($className);

        $fieldDefinitions = \array_merge(
            $entityDefinition->fieldDefinitions(),
            self::normalizeFieldDefinitions($fieldDefinitionOverrides),
        );

        $fieldValues = \array_map(function (FieldDefinition\Resolvable $fieldDefinition) {
            return $this->fieldValueResolutionStrategy->resolveFieldValue(
                $this->faker,
                $this,
                $fieldDefinition,
            );
        }, $fieldDefinitions);

        foreach ($fieldValues as $fieldName => $fieldValue) {
            $this->setField(
                $entity,
                $entityDefinition,
                $fieldName,
                $fieldValue,
            );
        }

        $afterCreate = $entityDefinition->afterCreate();

        $afterCreate(
            $entity,
            $fieldValues,
            $this->faker,
        );

        if ($this->persistAfterCreate && false === $classMetadata->isEmbeddedClass) {
            $this->entityManager->persist($entity);
        }

        return $entity;
    }

    /**
     * Creates an array of entities with all of their dependencies.
     *
     * @template T of object
     *
     * @param class-string<T>                                          $className
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitionOverrides
     *
     * @return list<T>
     */
    public function createMany(
        string $className,
        Count $count,
        array $fieldDefinitionOverrides = [],
    ): array {
        $resolved = $this->countResolutionStrategy->resolveCount(
            $this->faker,
            $count,
        );

        if (0 === $resolved) {
            return [];
        }

        return \array_map(function () use ($className, $fieldDefinitionOverrides) {
            return $this->createOne(
                $className,
                $fieldDefinitionOverrides,
            );
        }, \range(1, $resolved));
    }

    /**
     * Returns a fixture factory that utilizes the WithOptionalStrategy.
     *
     * With this strategy
     *
     * - an optional reference is always resolved
     * - references resolve to an array containing at least one reference
     */
    public function withOptional(): self
    {
        $instance = clone $this;

        $instance->fieldValueResolutionStrategy = new FieldResolution\FieldValueResolution\WithOptionalFieldValue();
        $instance->countResolutionStrategy = new FieldResolution\CountResolution\BetweenMinimumAndMaximumGreaterThanZeroCount();

        return $instance;
    }

    /**
     * Returns a fixture factory that utilizes the WithoutOptionalStrategy.
     *
     * With this strategy
     *
     * - an optional reference is never resolved
     * - references resolve to an array containing only the minimum number of references
     */
    public function withoutOptional(): self
    {
        $instance = clone $this;

        $instance->fieldValueResolutionStrategy = new FieldResolution\FieldValueResolution\WithoutOptionalFieldValue();
        $instance->countResolutionStrategy = new FieldResolution\CountResolution\MinimumCount();

        return $instance;
    }

    /**
     * Returns a fixture factory that persists entities after creation.
     */
    public function persisting(): self
    {
        $instance = clone $this;

        $instance->persistAfterCreate = true;

        return $instance;
    }

    /**
     * @param array<string, \Closure|FieldDefinition\Resolvable|mixed> $fieldDefinitions
     *
     * @return array<string, FieldDefinition\Resolvable>
     */
    private static function normalizeFieldDefinitions(array $fieldDefinitions): array
    {
        return \array_map(static function ($fieldDefinition): FieldDefinition\Resolvable {
            if ($fieldDefinition instanceof FieldDefinition\Resolvable) {
                return $fieldDefinition;
            }

            if ($fieldDefinition instanceof \Closure) {
                return FieldDefinition::closure($fieldDefinition);
            }

            return FieldDefinition::value($fieldDefinition);
        }, $fieldDefinitions);
    }

    private function setField(
        object $entity,
        EntityDefinition $entityDefinition,
        string $fieldName,
        mixed $fieldValue,
    ): void {
        $classMetadata = $entityDefinition->classMetadata();

        if ($classMetadata->isCollectionValuedAssociation($fieldName)) {
            $classMetadata->setFieldValue(
                $entity,
                $fieldName,
                self::collectionFrom($fieldValue),
            );

            return;
        }

        $classMetadata->setFieldValue(
            $entity,
            $fieldName,
            $fieldValue,
        );

        if (!\is_object($fieldValue)) {
            return;
        }

        if (!$classMetadata->isSingleValuedAssociation($fieldName)) {
            return;
        }

        $this->updateCollectionSideOfAssociation(
            $entity,
            $classMetadata,
            $fieldName,
            $fieldValue,
        );
    }

    private static function collectionFrom(mixed $value = []): Common\Collections\ArrayCollection
    {
        if (!\is_array($value)) {
            return new Common\Collections\ArrayCollection();
        }

        return new Common\Collections\ArrayCollection($value);
    }

    private function updateCollectionSideOfAssociation(
        object $entity,
        ORM\Mapping\ClassMetadata $classMetadata,
        string $fieldName,
        object $fieldValue,
    ): void {
        $inversedBy = $this->resolveInversedBy(
            $classMetadata,
            $fieldName,
        );

        if (!\is_string($inversedBy)) {
            return;
        }

        $classMetadataOfFieldValue = $this->entityManager->getClassMetadata($fieldValue::class);

        $collection = $classMetadataOfFieldValue->getFieldValue(
            $fieldValue,
            $inversedBy,
        );

        if (!$collection instanceof Common\Collections\Collection) {
            return;
        }

        $collection->add($entity);
    }

    private function resolveInversedBy(
        ORM\Mapping\ClassMetadata $classMetadata,
        string $fieldName,
    ): ?string {
        $association = $classMetadata->getAssociationMapping($fieldName);

        if (\is_array($association)) {
            return $association['inversedBy'];
        }

        if ($association instanceof ORM\Mapping\AssociationMapping) {
            return $association->inversedBy;
        }

        throw new \RuntimeException('This should not happen');
    }
}
