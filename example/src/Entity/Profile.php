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
#[ORM\Mapping\Table(name: 'profile')]
class Profile
{
    #[ORM\Mapping\Column(
        type: 'string',
        length: 36,
    )]
    #[ORM\Mapping\GeneratedValue(strategy: 'NONE')]
    #[ORM\Mapping\Id()]
    private string $id;

    #[ORM\Mapping\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    #[ORM\Mapping\OneToOne(
        targetEntity: User::class,
        inversedBy: 'profile',
    )]
    private User $user;

    #[ORM\Mapping\Column(
        name: 'bio',
        type: 'string',
    )]
    private string $bio;

    public function __construct(
        User $user,
        string $bio,
    ) {
        $this->id = Uuid\Uuid::uuid4()->toString();
        $this->user = $user;
        $this->bio = $bio;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function bio(): string
    {
        return $this->bio;
    }
}
