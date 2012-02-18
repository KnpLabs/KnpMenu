<?php

namespace Knp\Menu\Provider;

interface MenuProviderInterface
{
    /**
     * Retrieves a menu by its name
     *
     * @param string $name
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    function get($name, array $options = array());

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @param array $options
     * @return bool
     */
    function has($name, array $options = array());
}
