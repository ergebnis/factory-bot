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

use Doctrine\ORM;

/**
 * @ORM\Mapping\Entity
 * @ORM\Mapping\Table(name="repository")
 */
class Repository
{
    /**
     * @ORM\Mapping\Id
     * @ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @ORM\Mapping\Column(
     *     name="id",
     *     type="integer"
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Mapping\Column(
     *     name="name",
     *     type="string"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Mapping\ManyToOne(
     *     targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Organization",
     *     inversedBy="repositories"
     * )
     * @ORM\Mapping\JoinColumn(
     *     name="organization_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     *
     * @var Organization
     */
    private $organization;

    /**
     * @ORM\Mapping\ManyToOne(targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Repository")
     * @ORM\Mapping\JoinColumn(
     *     name="template_id",
     *     referencedColumnName="id"
     * )
     *
     * @var null|Repository
     */
    private $template;

    public function __construct(Organization $organization, string $name, ?self $template = null)
    {
        $this->organization = $organization;
        $this->name = $name;
        $this->template = $template;
    }

    public function id(): ?int
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
}
