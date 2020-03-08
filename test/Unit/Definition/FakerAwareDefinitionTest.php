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

namespace Ergebnis\FactoryBot\Test\Unit\Definition;

use Ergebnis\FactoryBot\Definition\Definition;
use Ergebnis\FactoryBot\Definition\FakerAwareDefinition;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @coversNothing
 */
final class FakerAwareDefinitionTest extends Framework\TestCase
{
    use Helper;

    public function testExtendsDefinitionInterface(): void
    {
        self::assertInterfaceExtends(Definition::class, FakerAwareDefinition::class);
    }
}
