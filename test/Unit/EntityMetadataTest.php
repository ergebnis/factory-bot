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

use Ergebnis\FactoryBot\EntityMetadata;
use Ergebnis\FactoryBot\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(EntityMetadata::class)]
final class EntityMetadataTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsEntityMetadata(): void
    {
        $className = \stdClass::class;

        /** @var list<string> $fieldNames */
        $fieldNames = self::faker()->words();

        $entityMetadata = EntityMetadata::create(
            $className,
            $fieldNames,
        );

        self::assertSame($className, $entityMetadata->className());
        self::assertSame($fieldNames, $entityMetadata->fieldNames());
    }
}
