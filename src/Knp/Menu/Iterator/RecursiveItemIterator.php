<?php

namespace Knp\Menu\Iterator;

use Knp\Menu\ItemInterface;

/**
 * Recursive iterator iterating on an item
 *
 * @template TKey
 *
 * @extends \IteratorIterator<TKey, ItemInterface, \Traversable<TKey, ItemInterface>>
 *
 * @implements \RecursiveIterator<TKey, ItemInterface>
 *
 * @final since 3.8.0
 */
class RecursiveItemIterator extends \IteratorIterator implements \RecursiveIterator
{
    /**
     * @param \Traversable<TKey, ItemInterface> $iterator
     */
    final public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
    }

    public function hasChildren(): bool
    {
        return 0 < \count($this->current());
    }

    /**
     * @return RecursiveItemIterator<TKey>
     */
    #[\ReturnTypeWillChange]
    public function getChildren()
    {
        return new static($this->current());
    }
}
