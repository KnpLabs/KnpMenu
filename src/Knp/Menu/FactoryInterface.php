<?php

namespace Knp\Menu;

/**
 * Interface implemented by the factory to create items
 */
interface FactoryInterface
{
    /**
     * Creates a menu item
     *
     * @param array $options
     */
    public function createItem(string $name, array $options = []): ItemInterface;
}
