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
 * @ORM\Table(name="person")
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Spaceship",
     *     inversedBy="crew"
     * )
     * @ORM\JoinColumn(
     *     name="spaceship_id",
     *     referencedColumnName="id",
     *     nullable=true
     * )
     *
     * @var null|SpaceShip
     */
    protected $spaceship;

    public function __construct(string $name, ?Spaceship $spaceship = null)
    {
        $this->name = $name;
        $this->spaceship = $spaceship;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSpaceship(): ?Spaceship
    {
        return $this->spaceship;
    }
}
