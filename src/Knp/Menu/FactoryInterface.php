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
     * @param string $name
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    function createItem($name, array $options = array());

    /**
     * Create a menu item from a NodeInterface
     *
     * @param \Knp\Menu\NodeInterface $node
     * @return \Knp\Menu\ItemInterface
     */
    function createFromNode(NodeInterface $node);

    /**
     * Creates a new menu item (and tree if $data['children'] is set).
     *
     * The source is an array of data that should match the output from MenuItem->toArray().
     *
     * @param  array $data The array of data to use as a source for the menu tree
     * @return \Knp\Menu\ItemInterface
     */
    function createFromArray(array $data);
}
