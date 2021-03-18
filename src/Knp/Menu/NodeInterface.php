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
     */
    public function getName(): string;

    /**
     * Get the options for the factory to create the item for this node
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array;

    /**
     * Get the child nodes implementing NodeInterface
     *
     * @return \Traversable<int, self>
     */
    public function getChildren(): \Traversable;
}
