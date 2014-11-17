<?php

namespace Knp\Menu;

/**
 * Interface implemented by the factory to create items
 *
 * @package Knp\Menu
 */
interface FactoryInterface
{
    /**
     * Creates a menu item
     *
     * @param string $name
     * @param array  $options
     *
     * @return ItemInterface
     */
    public function createItem($name, array $options = array());
}
