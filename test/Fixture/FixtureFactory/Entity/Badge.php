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
 * @ORM\Table(name="badge")
 */
class Badge
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\ManyToOne(targetEntity="\Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Person")
     * @ORM\JoinColumn(
     *     name="person_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     *
     * @var Person
     */
    protected $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getOwner(): ?Person
    {
        return $this->owner;
    }
}
