<?php

namespace Knp\Menu\Iterator;

use Knp\Menu\Matcher\MatcherInterface;

/**
 * Filter iterator keeping only current items
 */
class CurrentItemFilterIterator extends \FilterIterator
{
    /**
     * @param \Iterator<string|int, \Knp\Menu\ItemInterface> $iterator
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
