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
 * @ORM\Table(name="project")
 */
class Project
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
     *     name="name",
     *     type="string"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Repository")
     * @ORM\JoinColumn(
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
