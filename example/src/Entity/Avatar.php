<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Example\Entity;

use Doctrine\ORM;

#[ORM\Mapping\Embeddable()]
final class Avatar
{
    #[ORM\Mapping\Column(
        name: 'url',
        type: 'string',
    )]
    private string $url = '';

    #[ORM\Mapping\Column(
        name: 'width',
        type: 'integer',
    )]
    private int $width = 0;

    #[ORM\Mapping\Column(
        name: 'height',
        type: 'integer',
    )]
    private int $height = 0;

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
