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

namespace Ergebnis\FactoryBot\Test\AutoReview;

use Ergebnis\FactoryBot;
use Ergebnis\Test\Util;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @coversNothing
 */
final class SrcCodeTest extends Framework\TestCase
{
    use Util\Helper;

    public function testSrcClassesHaveUnitTests(): void
    {
        self::assertClassesHaveTests(
            __DIR__ . '/../../src/',
            'Ergebnis\\FactoryBot\\',
            'Ergebnis\\FactoryBot\\Test\\Unit',
            [
                FactoryBot\Strategy\DefaultStrategy::class,
            ],
        );
    }
}
