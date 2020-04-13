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

use Doctrine\Common;
use Doctrine\ORM;

/**
 * @ORM\Mapping\Entity
 * @ORM\Mapping\Table(name="space_ship")
 */
class Organization
{
    /**
     * @ORM\Mapping\Id
     * @ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @ORM\Mapping\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Mapping\Column(
     *     name="is_verified",
     *     type="boolean"
     * )
     *
     * @var bool
     */
    private $isVerified = false;

    /**
     * @ORM\Mapping\Column(
     *     name="name",
     *     type="string"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Mapping\OneToMany(
     *     targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Repository",
     *     mappedBy="organization"
     * )
     *
     * @var Common\Collections\ArrayCollection<int, Repository>
     */
    private $repositories;

    /**
     * @ORM\Mapping\ManyToMany(
     *     targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\User",
     *     inversedBy="organizations"
     * )
     *
     * @var Common\Collections\ArrayCollection<int, User>
     */
    private $members;

    /**
     * @var bool
     */
    private $constructorWasCalled = false;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->repositories = new Common\Collections\ArrayCollection();
        $this->members = new Common\Collections\ArrayCollection();
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
