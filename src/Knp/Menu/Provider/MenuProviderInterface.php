<?php

namespace Knp\Menu\Provider;

use Knp\Menu\ItemInterface;

interface MenuProviderInterface
{
    /**
     * Retrieves a menu by its name
     *
     * @param string $name
     * @param array  $options
     *
     * @return ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get(string $name, array $options = []): ItemInterface;

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @param array  $options
     *
     * @return boolean
     */
    public function has(string $name, array $options = []): bool;
}
