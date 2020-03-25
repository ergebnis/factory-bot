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
 * @ORM\Embeddable
 */
final class Avatar
{
    /**
     * @ORM\Column(
     *     name="url",
     *     type="string"
     * )
     *
     * @var string
     */
    private $url = '';

    /**
     * @ORM\Column(
     *     name="width",
     *     type="integer"
     * )
     *
     * @var int
     */
    private $width = 0;

    /**
     * @ORM\Column(
     *     name="height",
     *     type="integer"
     * )
     *
     * @var int
     */
    private $height = 0;

    public function url(): string
    {
        return $this->url;
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }
}
