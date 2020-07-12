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

namespace Ergebnis\FactoryBot;

use Ergebnis\Classy;

final class Definitions
{
    /**
     * @psalm-var list<Definition>
     *
     * @var array<int, Definition>
     */
    private $definitions = [];

    private function __construct()
    {
    }

    /**
     * Creates a new instance of this class, and collects all definitions found in the specified directory.
     *
     * @param string $directory
     *
     * @throws Exception\InvalidDefinition
     * @throws Exception\InvalidDirectory
     *
     * @return self
     */
    public static function in(string $directory): self
    {
        if (!\is_dir($directory)) {
            throw Exception\InvalidDirectory::notDirectory($directory);
        }

        $instance = new self();

        $constructs = Classy\Constructs::fromDirectory($directory);

        foreach ($constructs as $construct) {
            /** @var class-string $className */
            $className = $construct->name();

            try {
                $reflection = new \ReflectionClass($className);
            } catch (\ReflectionException $exception) {
                continue;
            }

            if (!$reflection->implementsInterface(Definition::class)) {
                continue;
            }

            if ($reflection->isAbstract()) {
                continue;
            }

            if (!$reflection->isInstantiable()) {
                throw Exception\InvalidDefinition::canNotBeInstantiated($className);
            }

            try {
                /** @var Definition $definition */
                $definition = $reflection->newInstance();
            } catch (\Exception $exception) {
                throw Exception\InvalidDefinition::throwsExceptionDuringInstantiation(
                    $className,
                    $exception
                );
            }

            $instance->definitions[] = $definition;
        }

        return $instance;
    }

    /**
     * Registers all found definitions with the specified fixture factory.
     *
     * @param FixtureFactory $fixtureFactory
     */
    public function registerWith(FixtureFactory $fixtureFactory): void
    {
        foreach ($this->definitions as $definition) {
            $definition->accept($fixtureFactory);
        }
    }
}
