<?php

namespace Knp\Menu;

/**
 * Interface implemented by a node to construct a menu from a tree.
 */
interface NodeInterface
{
    /**
     * Get the name of the node
     *
     * Each child of a node must have a unique name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the options for the factory to create the item for this node
     *
     * @return array
     */
    public function getOptions();

    /**
     * Get the child nodes implementing NodeInterface
     *
     * @return \Traversable
     */
    public function getChildren();
}
