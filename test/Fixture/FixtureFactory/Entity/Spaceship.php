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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="space_ship")
 */
class Spaceship
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
     * @ORM\Column(
     *     name="type",
     *     nullable=false
     * )
     *
     * @var string
     */
    protected $type = 'cruiser';

    /**
     * @ORM\Column(
     *     name="name",
     *     nullable=true
     * )
     *
     * @var null|string
     */
    protected $name;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Person",
     *     mappedBy="spaceship"
     * )
     *
     * @var ArrayCollection<Person>
     */
    protected $crew;

    /**
     * @var bool
     */
    protected $constructorWasCalled = false;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->crew = new ArrayCollection();
        $this->constructorWasCalled = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection<Person>
     */
    public function getCrew(): ArrayCollection
    {
        return $this->crew;
    }

    public function constructorWasCalled(): bool
    {
        return $this->constructorWasCalled;
    }
}
