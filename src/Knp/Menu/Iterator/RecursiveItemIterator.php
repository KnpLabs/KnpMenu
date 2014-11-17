<?php

namespace Knp\Menu\Iterator;

/**
 * Recursive iterator iterating on an item
 *
 * @package Knp\Menu\Iterator
 */
class RecursiveItemIterator extends \IteratorIterator implements \RecursiveIterator
{
    /**
     * @return bool
     */
    public function hasChildren()
    {
        return 0 < count($this->current());
    }

    /**
     * @return static
     */
    public function getChildren()
    {
        return new static($this->current());
    }
}
