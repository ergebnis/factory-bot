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
 * @ORM\Entity
 * @ORM\Table(name="person")
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column
     */
    protected $name;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="SpaceShip",
     *     inversedBy="crew"
     * )
     * @ORM\JoinColumn(
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
