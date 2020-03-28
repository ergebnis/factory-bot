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

namespace Ergebnis\FactoryBot\Test\Unit;

use Doctrine\ORM;
use Ergebnis\FactoryBot\EntityDefinition;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryBot\EntityDefinition
 */
final class EntityDefinitionTest extends Framework\TestCase
{
    use Helper;

    public function testConstructorSetsValues(): void
    {
        $classMetadata = $this->prophesize(ORM\Mapping\ClassMetadata::class);

        $fieldDefinitions = [
            'foo' => static function (): string {
                return 'bar';
            },
            'bar' => static function (): string {
                return 'baz';
            },
        ];

        $configuration = [
            'afterCreate' => static function ($entity, array $fieldValues): void {
                // intentionally left blank
            },
        ];

        $entityDefiniton = new EntityDefinition(
            $classMetadata->reveal(),
            $fieldDefinitions,
            $configuration
        );

        self::assertSame($classMetadata->reveal(), $entityDefiniton->classMetadata());
        self::assertSame($configuration, $entityDefiniton->configuration());
        self::assertSame($fieldDefinitions, $entityDefiniton->fieldDefinitions());
    }
}
