<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-girl-definition
 */

namespace Ergebnis\FactoryGirl\Definition\Test\Unit;

use Ergebnis\FactoryGirl\Definition\AbstractDefinition;
use Ergebnis\FactoryGirl\Definition\FakerAwareDefinition;
use Ergebnis\FactoryGirl\Definition\Test\Fixture;
use Ergebnis\Test\Util\Helper;
use Faker\Generator;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\FactoryGirl\Definition\AbstractDefinition
 */
final class AbstractDefinitionTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsFakerAwareDefinitionInterface(): void
    {
        self::assertClassImplementsInterface(FakerAwareDefinition::class, AbstractDefinition::class);
    }

    public function testFakerThrowsBadMethodCallExceptionIfDefinitionHasNotBeenProvidedWithFaker(): void
    {
        $definition = new Fixture\Definition\ExtendsAbstractDefinition\UserDefinition();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(\sprintf(
            'Before accessing, an instance of "%s" needs to be provided using provideWith()',
            Generator::class
        ));

        $definition->faker();
    }

    public function testFakerReturnsFakerWhenProvidedWithIt(): void
    {
        $faker = new Generator();

        $definition = new Fixture\Definition\ExtendsAbstractDefinition\UserDefinition();

        $definition->provideWith($faker);

        self::assertSame($faker, $definition->faker());
    }
}
