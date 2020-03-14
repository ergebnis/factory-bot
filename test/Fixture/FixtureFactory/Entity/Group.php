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

namespace Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="group")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
