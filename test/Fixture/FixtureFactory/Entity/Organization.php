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

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="space_ship")
 */
class Organization
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(
     *     name="is_verified",
     *     type="boolean"
     * )
     *
     * @var bool
     */
    private $isVerified = false;

    /**
     * @ORM\Column(
     *     name="name",
     *     type="string"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Repository",
     *     mappedBy="organization"
     * )
     *
     * @var Collections\ArrayCollection<int, Repository>
     */
    private $repositories;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\User",
     *     inversedBy="organizations"
     * )
     *
     * @var Collections\ArrayCollection<int, User>
     */
    private $members;

    /**
     * @var bool
     */
    private $constructorWasCalled = false;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->repositories = new Collections\ArrayCollection();
        $this->members = new Collections\ArrayCollection();
        $this->constructorWasCalled = true;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function renameTo(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<int, Repository>
     */
    public function repositories(): array
    {
        return $this->repositories->toArray();
    }

    /**
     * @return array<int, User>
     */
    public function members(): array
    {
        return $this->members->toArray();
    }

    public function constructorWasCalled(): bool
    {
        return $this->constructorWasCalled;
    }
}
