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

namespace Example\Entity;

use Doctrine\ORM;

/**
 * @ORM\Mapping\Entity
 * @ORM\Mapping\Table(name="project")
 */
class Project
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
     *     name="name",
     *     type="string"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Mapping\ManyToOne(targetEntity="Example\Entity\Repository")
     * @ORM\Mapping\JoinColumn(
     *     name="repository_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     *
     * @var Repository
     */
    private $repository;

    public function __construct(string $name, Repository $repository)
    {
        $this->name = $name;
        $this->repository = $repository;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function repository(): Repository
    {
        return $this->repository;
    }
}
