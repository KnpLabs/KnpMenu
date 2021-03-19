<?php

namespace Knp\Menu\Provider;

use Knp\Menu\ItemInterface;

interface MenuProviderInterface
{
    /**
     * Retrieves a menu by its name
     *
     * @phpstan-param array<string, callable|array{\Closure, string}> $options
     *
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get(string $name, array $options = []): ItemInterface;

    /**
     * Checks whether a menu exists in this provider
     *
     * @phpstan-param array<string, callable|array{\Closure, string}> $options
     */
    public function has(string $name, array $options = []): bool;
}
