<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2021 Andreas Möller
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
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\EntityDefinition
 *
 * @uses \Ergebnis\FactoryBot\Exception\InvalidFieldDefinitions
 * @uses \Ergebnis\FactoryBot\FieldDefinition
 * @uses \Ergebnis\FactoryBot\FieldDefinition\Value
 */
final class EntityDefinitionTest extends Framework\TestCase
{
    use Helper;

    /**
     * @dataProvider \Ergebnis\FactoryBot\Test\DataProvider\ValueProvider::arbitrary()
     *
     * @param mixed $fieldDefinition
     */
    public function testConstructorRejectsFieldDefinitionsWhenValuesAreNotFieldDefinitions($fieldDefinition): void
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
            }
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
            $afterCreate
        );

        self::assertSame($classMetadata, $entityDefiniton->classMetadata());
        self::assertSame($fieldDefinitions, $entityDefiniton->fieldDefinitions());
        self::assertSame($afterCreate, $entityDefiniton->afterCreate());
    }
}
