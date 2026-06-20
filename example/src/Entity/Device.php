<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2026 Andreas Möller
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
#[ORM\Mapping\Table(name: 'device')]
class Device
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
        name: 'fingerprint_id',
        referencedColumnName: 'id',
    )]
    #[ORM\Mapping\OneToOne(
        targetEntity: Fingerprint::class,
        inversedBy: 'device',
    )]
    private ?Fingerprint $fingerprint;

    public function __construct(string $name, ?Fingerprint $fingerprint = null)
    {
        $this->id = Uuid\Uuid::uuid4()->toString();
        $this->name = $name;
        $this->fingerprint = $fingerprint;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function fingerprint(): ?Fingerprint
    {
        return $this->fingerprint;
    }
}
