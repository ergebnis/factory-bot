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
 * @ORM\Table(name="commander")
 */
class Commander
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *     name="id",
     *     type="integer"
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Embedded(
     *     class="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Name",
     *     columnPrefix=false
     * )
     *
     * @var Name
     */
    private $name;

    public function __construct()
    {
        $this->name = new Name();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): ?Name
    {
        return $this->name;
    }
}
