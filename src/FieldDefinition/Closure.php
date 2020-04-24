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

use Ergebnis\FactoryBot\FixtureFactory;

/**
 * @internal
 */
final class Closure implements Resolvable
{
    /**
     * @var \Closure
     */
    private $closure;

    private function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public static function required(\Closure $closure): self
    {
        return new self($closure);
    }

    public function resolve(FixtureFactory $fixtureFactory)
    {
        $closure = $this->closure;

        return $closure($fixtureFactory);
    }
}
