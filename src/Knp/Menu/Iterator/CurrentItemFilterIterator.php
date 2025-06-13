<?php

namespace Knp\Menu\Iterator;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;

/**
 * Filter iterator keeping only current items
 *
 * @template TKey
 * @template-extends \FilterIterator<TKey, ItemInterface, \Iterator<TKey, ItemInterface>>
 */
class CurrentItemFilterIterator extends \FilterIterator
{
    /**
     * @param \Iterator<TKey, ItemInterface> $iterator
     */
    public function __construct(\Iterator $iterator, private MatcherInterface $matcher)
    {

        parent::__construct($iterator);
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function accept()
    {
        return $this->matcher->isCurrent($this->current());
    }
}
