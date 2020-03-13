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

use Doctrine\ORM;

/**
 * @ORM\Mapping\Entity
 */
class Person
{
    /**
     * @ORM\Mapping\Id
     * @ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @ORM\Mapping\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Mapping\Column
     */
    protected $name;

    /**
     * @ORM\Mapping\ManyToOne(
     *     targetEntity="SpaceShip",
     *     inversedBy="crew"
     * )
     * @ORM\Mapping\JoinColumn(
     *     name="spaceShip_id",
     *     referencedColumnName="id",
     *     nullable=true
     * )
     */
    protected $spaceShip;

    public function __construct($name, ?SpaceShip $spaceShip = null)
    {
        $this->name = $name;
        $this->spaceShip = $spaceShip;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSpaceShip()
    {
        return $this->spaceShip;
    }
}
