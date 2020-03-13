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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Badge
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column
     */
    protected $label;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(
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
