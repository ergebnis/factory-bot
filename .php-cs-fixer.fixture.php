<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

use Ergebnis\License;
use Ergebnis\PhpCsFixer;

$license = License\Type\MIT::markdown(
    __DIR__ . '/LICENSE.md',
    License\Range::since(
        License\Year::fromString('2020'),
        new \DateTimeZone('UTC'),
    ),
    License\Holder::fromString('Andreas Möller'),
    License\Url::fromString('https://github.com/ergebnis/factory-bot'),
);

$license->save();

$config = PhpCsFixer\Config\Factory::fromRuleSet(new PhpCsFixer\Config\RuleSet\Php74($license->header()), [
    'mb_str_functions' => false,
    'psr_autoloading' => false,
]);

$config->getFinder()->in(__DIR__ . '/test/Fixture/DefinitionProvider/CanNotBeAutoloaded');

$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/php-cs-fixer.fixture.cache');

return $config;
