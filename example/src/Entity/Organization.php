<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Example\Entity;

use Doctrine\Common;
use Doctrine\ORM;
use Ramsey\Uuid;

/**
 * @ORM\Mapping\Entity
 * @ORM\Mapping\Table(name="organization")
 */
class Organization
{
    /**
     * @ORM\Mapping\Id
     * @ORM\Mapping\GeneratedValue(strategy="NONE")
     * @ORM\Mapping\Column(
     *     type="string",
     *     length=36
     * )
     */
    private string $id;

    /**
     * @ORM\Mapping\Column(
     *     name="is_verified",
     *     type="boolean"
     * )
     */
    private bool $isVerified = false;

    /**
     * @ORM\Mapping\Column(
     *     name="name",
     *     type="string"
     * )
     */
    private string $name;

    /**
     * @ORM\Mapping\Column(
     *     name="url",
     *     type="string",
     *     nullable=true
     * )
     */
    private ?string $url = null;

    /**
     * @ORM\Mapping\OneToMany(
     *     targetEntity="Example\Entity\Repository",
     *     mappedBy="organization"
     * )
     *
     * @var Common\Collections\Collection<int, Repository>
     */
    private Common\Collections\Collection $repositories;

    /**
     * @ORM\Mapping\ManyToMany(
     *     targetEntity="Example\Entity\User",
     *     inversedBy="organizations"
     * )
     *
     * @var Common\Collections\Collection<int, User>
     */
    private Common\Collections\Collection$members;
    private bool $constructorWasCalled = false;

    public function __construct(string $name)
    {
        $this->id = Uuid\Uuid::uuid4()->toString();
        $this->name = $name;
        $this->repositories = new Common\Collections\ArrayCollection();
        $this->members = new Common\Collections\ArrayCollection();
        $this->constructorWasCalled = true;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): ?string
    {
        return $this->url;
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
