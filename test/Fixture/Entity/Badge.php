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

namespace Ergebnis\FactoryBot\Test\Fixture\Entity;

use Doctrine\ORM;

/**
 * @ORM\Mapping\Entity
 */
class Badge
{
    /**
     * @ORM\Mapping\Id
     * @ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @ORM\Mapping\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Mapping\Column
     */
    protected $label;

    /**
     * @ORM\Mapping\ManyToOne(targetEntity="Person")
     * @ORM\Mapping\JoinColumn(
     *     name="person_id",
     *     referencedColumnName="id",
     *     nullable=true
     * )
     */
    protected $owner;

    public function __construct($label, Person $owner)
    {
        $this->label = $label;
        $this->owner = $owner;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getOwner()
    {
        return $this->owner;
    }
}
