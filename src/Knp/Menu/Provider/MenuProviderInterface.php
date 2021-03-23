<?php

namespace Knp\Menu\Provider;

use Knp\Menu\ItemInterface;

interface MenuProviderInterface
{
    /**
     * Retrieves a menu by its name
     *
     * @param array<string, mixed> $options
     *
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get(string $name, array $options = []): ItemInterface;

    /**
     * Checks whether a menu exists in this provider
     *
     * @param array<string, mixed> $options
     */
    public function has(string $name, array $options = []): bool;
}
