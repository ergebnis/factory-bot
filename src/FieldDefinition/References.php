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

namespace Ergebnis\FactoryBot\FieldDefinition;

use Ergebnis\FactoryBot\Exception;
use Ergebnis\FactoryBot\FixtureFactory;

/**
 * @internal
 */
final class References implements Resolvable
{
    private $className;

    private $count;

    /**
     * @param class-string $className
     * @param int          $count
     *
     * @throws Exception\InvalidCount
     */
    public function __construct(string $className, int $count)
    {
        if (1 > $count) {
            throw Exception\InvalidCount::notGreaterThanOrEqualTo(
                1,
                $count
            );
        }

        $this->className = $className;
        $this->count = $count;
    }

    public function resolve(FixtureFactory $fixtureFactory): array
    {
        return $fixtureFactory->getList(
            $this->className,
            [],
            $this->count
        );
    }
}
