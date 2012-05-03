<?php

namespace Knp\Menu\Iterator;

/**
 * Recursive iterator iterating on an item
 */
class ItemIterator extends \ArrayIterator implements \RecursiveIterator
{
    public function hasChildren()
    {
        return $this->current()->getIterator() instanceof \RecursiveIterator;
    }

    public function getChildren()
    {
        return $this->current()->getIterator();
    }
}
