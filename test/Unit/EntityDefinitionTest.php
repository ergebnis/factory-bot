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

namespace Ergebnis\FactoryBot\Test\Unit;

use Doctrine\ORM;
use Ergebnis\FactoryBot\EntityDefinition;
use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(EntityDefinition::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidFieldDefinitions::class)]
#[Framework\Attributes\UsesClass(FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FieldDefinition\Value::class)]
final class EntityDefinitionTest extends Framework\TestCase
{
    use Test\Util\Helper;

    #[Framework\Attributes\DataProviderExternal(Test\DataProvider\ValueProvider::class, 'arbitrary')]
    public function testConstructorRejectsFieldDefinitionsWhenValuesAreNotFieldDefinitions(mixed $fieldDefinition): void
    {
        $fieldDefinitions = [
            'foo' => FieldDefinition::value('bar'),
            'bar' => $fieldDefinition,
        ];

        $this->expectException(Exception\InvalidFieldDefinitions::class);

        new EntityDefinition(
            $this->createMock(ORM\Mapping\ClassMetadata::class),
            $fieldDefinitions,
            static function ($entity, array $fieldValues): void {
                // intentionally left blank
            },
        );
    }

    public function testConstructorSetsValues(): void
    {
        $classMetadata = $this->createMock(ORM\Mapping\ClassMetadata::class);

        $fieldDefinitions = [
            'foo' => FieldDefinition::value('bar'),
            'bar' => FieldDefinition::value('baz'),
        ];

        $afterCreate = static function ($entity, array $fieldValues): void {
            // intentionally left blank
        };

        $entityDefiniton = new EntityDefinition(
            $classMetadata,
            $fieldDefinitions,
            $afterCreate,
        );

        self::assertSame($classMetadata, $entityDefiniton->classMetadata());
        self::assertSame($fieldDefinitions, $entityDefiniton->fieldDefinitions());
        self::assertSame($afterCreate, $entityDefiniton->afterCreate());
    }
}
