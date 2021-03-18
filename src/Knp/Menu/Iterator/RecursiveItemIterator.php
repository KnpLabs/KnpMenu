<?php

namespace Knp\Menu\Iterator;

/**
 * Recursive iterator iterating on an item
 *
 * @extends \IteratorIterator<string, \Knp\Menu\ItemInterface, \Traversable<string|int, \Knp\Menu\ItemInterface>>
 */
class RecursiveItemIterator extends \IteratorIterator implements \RecursiveIterator
{
    public function hasChildren(): bool
    {
        return 0 < \count($this->current());
    }

    public function getChildren()
    {
        return new static($this->current());
    }
}
