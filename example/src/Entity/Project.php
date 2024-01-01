<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Example\Entity;

use Doctrine\ORM;
use Ramsey\Uuid;

#[ORM\Mapping\Entity()]
#[ORM\Mapping\Table(name: 'project')]
class Project
{
    #[ORM\Mapping\Id()]
    #[ORM\Mapping\GeneratedValue(strategy: 'NONE')]
    #[ORM\Mapping\Column(
        type: 'string',
        length: 36,
    )]
    private string $id;

    #[ORM\Mapping\Column(
        name: 'name',
        type: 'string',
    )]
    private string $name;

    #[ORM\Mapping\ManyToOne(targetEntity: Repository::class)]
    #[ORM\Mapping\JoinColumn(
        name: 'repository_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    private Repository $repository;

    public function __construct(
        string $name,
        Repository $repository,
    ) {
        $this->id = Uuid\Uuid::uuid4()->toString();
        $this->name = $name;
        $this->repository = $repository;
    }

    public function id(): string
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
