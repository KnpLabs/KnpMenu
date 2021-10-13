<?php

namespace Knp\Menu\Iterator;

use Knp\Menu\Matcher\MatcherInterface;

/**
 * Filter iterator keeping only current items
 */
class CurrentItemFilterIterator extends \FilterIterator
{
    /**
     * @var MatcherInterface
     */
    private $matcher;

    /**
     * @param \Iterator<string|int, \Knp\Menu\ItemInterface> $iterator
     */
    public function __construct(\Iterator $iterator, MatcherInterface $matcher)
    {
        $this->matcher = $matcher;

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
