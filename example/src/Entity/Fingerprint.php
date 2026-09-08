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
#[ORM\Mapping\Table(name: 'fingerprint')]
class Fingerprint
{
    #[ORM\Mapping\Column(
        name: 'id',
        type: 'string',
    )]
    #[ORM\Mapping\GeneratedValue(strategy: 'NONE')]
    #[ORM\Mapping\Id()]
    private string $id;

    #[ORM\Mapping\Column(
        name: 'token',
        type: 'string',
    )]
    private string $token;

    #[ORM\Mapping\OneToOne(
        targetEntity: Device::class,
        mappedBy: 'fingerprint',
    )]
    private ?Device $device = null;

    public function __construct(string $token)
    {
        $this->id = Uuid\Uuid::uuid4()->toString();
        $this->token = $token;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function device(): ?Device
    {
        return $this->device;
    }
}
