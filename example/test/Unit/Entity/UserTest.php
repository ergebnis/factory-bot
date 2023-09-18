<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Example\Test\Unit\Entity;

use Example\Entity;
use Example\Test;

/**
 * @covers \Example\Entity\User
 */
final class UserTest extends Test\Unit\AbstractTestCase
{
    public function testRenameToRenamesUser(): void
    {
        $login = self::faker()->userName();

        /** @var Entity\User $user */
        $user = self::fixtureFactory()->createOne(Entity\User::class);

        $user->renameTo($login);

        self::assertSame($login, $user->login());
    }
}
