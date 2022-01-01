<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Example\Test\AutoReview;

use Ergebnis\FactoryBot;
use Example\Test;

/**
 * @internal
 * @coversNothing
 */
final class FixtureTest extends Test\Unit\AbstractTestCase
{
    public function testEntitiesHaveEntityDefinitionProviders(): void
    {
        $mappingDriver = self::entityManager()->getConfiguration()->getMetadataDriverImpl();

        self::assertNotNull($mappingDriver);

        $entityClassNames = $mappingDriver->getAllClassNames();

        \sort($entityClassNames);

        $expectedProviderClassNames = \array_combine(
            $entityClassNames,
            \array_map(static function (string $entityClassName): string {
                return \str_replace(
                    'Example\\Entity\\',
                    'Example\\Test\\Fixture\\Entity\\',
                    $entityClassName,
                ) . 'DefinitionProvider';
            }, $entityClassNames),
        );

        self::assertIsArray($expectedProviderClassNames);

        $actualProviderClassNames = \array_filter($expectedProviderClassNames, static function (string $providerClassName): bool {
            try {
                $reflection = new \ReflectionClass($providerClassName);
            } catch (\ReflectionException $exception) {
                return false;
            }

            return $reflection->implementsInterface(FactoryBot\EntityDefinitionProvider::class);
        });

        $missingProviderClassNames = \array_diff(
            $expectedProviderClassNames,
            $actualProviderClassNames,
        );

        $message = \sprintf(
            <<<'TXT'
Failed asserting that entity provider definitions exist for the following entities:

 - %s

Expected the following entity provider definitions but either they do not exist or do not implement the "%s" interface:

 - %s

TXT
            ,
            \implode(
                \PHP_EOL . ' - ',
                \array_keys($missingProviderClassNames),
            ),
            FactoryBot\EntityDefinitionProvider::class,
            \implode(
                \PHP_EOL . ' - ',
                $missingProviderClassNames,
            ),
        );

        self::assertEmpty($missingProviderClassNames, $message);
    }
}
