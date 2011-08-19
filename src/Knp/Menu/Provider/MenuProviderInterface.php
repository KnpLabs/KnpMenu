<?php

namespace Knp\Menu\Provider;

interface MenuProviderInterface
{
    /**
     * Retrieves a menu by its name
     *
     * @param string $name
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    function get($name);

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @return bool
     */
    function has($name);
}
