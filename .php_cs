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

use Ergebnis\License;
use Ergebnis\PhpCsFixer\Config;

$license = License\Type\MIT::markdown(
    __DIR__ . '/LICENSE.md',
    License\Range::since(
        License\Year::fromString('2020'),
        new \DateTimeZone('UTC')
    ),
    License\Holder::fromString('Andreas Möller'),
    License\Url::fromString('https://github.com/ergebnis/factory-bot')
);

$license->save();

$config = Config\Factory::fromRuleSet(new Config\RuleSet\Php71($license->header()), [
    'final_class' => false,
    'mb_str_functions' => false,
]);

$config->getFinder()
    ->ignoreDotFiles(false)
    ->in(__DIR__)
    ->exclude([
        '.build/',
        '.dependabot/',
        '.github/',
        'src/Doctrine/',
        'test/Doctrine/',
    ])
    ->name('.php_cs');

$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php_cs.cache');

return $config;
