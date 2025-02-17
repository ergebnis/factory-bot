<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2025 Andreas Möller
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
#[ORM\Mapping\Table(name: 'repository')]
class Repository
{
    #[ORM\Mapping\Column(
        name: 'id',
        type: 'string',
    )]
    #[ORM\Mapping\GeneratedValue(strategy: 'NONE')]
    #[ORM\Mapping\Id()]
    private string $id;

    #[ORM\Mapping\Column(
        name: 'name',
        type: 'string',
    )]
    private string $name;

    #[ORM\Mapping\JoinColumn(
        name: 'organization_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    #[ORM\Mapping\ManyToOne(
        targetEntity: Organization::class,
        inversedBy: 'repositories',
    )]
    private Organization $organization;

    #[ORM\Mapping\JoinColumn(
        name: 'template_id',
        referencedColumnName: 'id',
    )]
    #[ORM\Mapping\ManyToOne(targetEntity: self::class)]
    private ?Repository $template;

    #[ORM\Mapping\JoinColumn(
        name: 'code_of_conduct_key',
        referencedColumnName: 'key',
    )]
    #[ORM\Mapping\ManyToOne(targetEntity: CodeOfConduct::class)]
    private ?CodeOfConduct $codeOfConduct;

    public function __construct(
        Organization $organization,
        string $name,
        ?self $template = null,
        ?CodeOfConduct $codeOfConduct = null,
    ) {
        $this->id = Uuid\Uuid::uuid4()->toString();
        $this->organization = $organization;
        $this->name = $name;
        $this->template = $template;
        $this->codeOfConduct = $codeOfConduct;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function organization(): Organization
    {
        return $this->organization;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function template(): ?self
    {
        return $this->template;
    }

    public function codeOfConduct(): ?CodeOfConduct
    {
        return $this->codeOfConduct;
    }
}
