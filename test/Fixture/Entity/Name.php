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

namespace Ergebnis\FactoryBot\Test\Fixture\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Name
{
    /**
     * @ORM\Column(
     *     name="first_name",
     *     type="string",
     *     length=100,
     *     nullable=true
     * )
     *
     * @var null|string
     */
    private $first;

    /**
     * @ORM\Column(
     *     name="last_name",
     *     type="string",
     *     length=100,
     *     nullable=true
     * )
     *
     * @var null|string
     */
    private $last;

    public function first(): ?string
    {
        return $this->first;
    }

    public function last(): ?string
    {
        return $this->last;
    }
}
