<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\Test\Unit;

use Doctrine\ORM;
use Ergebnis\FactoryBot;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(FactoryBot\EntityDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\Exception\InvalidFieldDefinitions::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition::class)]
#[Framework\Attributes\UsesClass(FactoryBot\FieldDefinition\Value::class)]
final class EntityDefinitionTest extends Framework\TestCase
{
    use FactoryBot\Test\Util\Helper;

    #[Framework\Attributes\DataProviderExternal(FactoryBot\Test\DataProvider\ValueProvider::class, 'arbitrary')]
    public function testCreateRejectsFieldDefinitionsWhenValuesAreNotFieldDefinitions(mixed $fieldDefinition): void
    {
        $fieldDefinitions = [
            'bar' => $fieldDefinition,
            'foo' => FactoryBot\FieldDefinition::value('bar'),
        ];

        $this->expectException(FactoryBot\Exception\InvalidFieldDefinitions::class);

        FactoryBot\EntityDefinition::create(
            $this->createMock(ORM\Mapping\ClassMetadata::class),
            $fieldDefinitions,
            static function ($entity, array $fieldValues): void {
                // intentionally left blank
            },
        );
    }

    public function testCreateReturnsEntityDefinition(): void
    {
        $classMetadata = $this->createMock(ORM\Mapping\ClassMetadata::class);

        $fieldDefinitions = [
            'bar' => FactoryBot\FieldDefinition::value('baz'),
            'foo' => FactoryBot\FieldDefinition::value('bar'),
        ];

        $afterCreate = static function ($entity, array $fieldValues): void {
            // intentionally left blank
        };

        $entityDefinition = FactoryBot\EntityDefinition::create(
            $classMetadata,
            $fieldDefinitions,
            $afterCreate,
        );

        self::assertSame($classMetadata, $entityDefinition->classMetadata());
        self::assertSame($fieldDefinitions, $entityDefinition->fieldDefinitions());
        self::assertSame($afterCreate, $entityDefinition->afterCreate());
    }
}
