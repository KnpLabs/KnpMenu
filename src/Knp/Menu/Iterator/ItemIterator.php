<?php

namespace Knp\Menu\Iterator;

/**
 * Recursive iterator iterating on an item
 */
class ItemIterator extends \ArrayIterator implements \RecursiveIterator
{
    public function hasChildren()
    {
        $current = $this->current();

        return $current->getIterator() instanceof \RecursiveIterator && 0 < count($current);
    }

    public function getChildren()
    {
        return $this->current()->getIterator();
    }
}
