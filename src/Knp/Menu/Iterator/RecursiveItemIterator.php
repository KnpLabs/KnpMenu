<?php

namespace Knp\Menu\Iterator;

/**
 * Recursive iterator iterating on an item
 */
class RecursiveItemIterator extends \IteratorIterator implements \RecursiveIterator
{
    public function hasChildren()
    {
        return 0 < count($this->current());
    }

    public function getChildren()
    {
        return new static($this->current());
    }
}
