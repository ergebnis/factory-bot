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
 * @ORM\Table(name="repository")
 */
class Repository
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
     *     name="name",
     *     type="string"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Organization",
     *     inversedBy="repositories"
     * )
     * @ORM\JoinColumn(
     *     name="organization_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     *
     * @var Organization
     */
    private $organization;

    /**
     * @ORM\ManyToOne(targetEntity="Ergebnis\FactoryBot\Test\Fixture\FixtureFactory\Entity\Repository")
     * @ORM\JoinColumn(
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
