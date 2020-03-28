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
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *     name="id",
     *     type="integer"
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(
     *     name="login",
     *     type="string"
     * )
     *
     * @var string
     */
    private $login;

    /**
     * @ORM\Embedded(
     *     class="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Avatar",
     *     columnPrefix="avatar"
     * )
     *
     * @var Avatar
     */
    private $avatar;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Organization",
     *     mappedBy="members"
     * )
     *
     * @var Collections\ArrayCollection<int, Organization>
     */
    private $organizations;

    public function __construct(string $login, Avatar $avatar)
    {
        $this->login = $login;
        $this->avatar = $avatar;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function login(): string
    {
        return $this->login;
    }

    public function avatar(): Avatar
    {
        return $this->avatar;
    }

    /**
     * @return array<int, Organization>
     */
    public function organizations(): array
    {
        return $this->organizations->toArray();
    }
}
