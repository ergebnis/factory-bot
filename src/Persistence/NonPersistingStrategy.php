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

namespace Ergebnis\FactoryBot\Persistence;

use Doctrine\ORM;

final class NonPersistingStrategy implements PersistenceStrategy
{
    public function persist(ORM\EntityManagerInterface $entityManager, object $entity): void
    {
    }
}
