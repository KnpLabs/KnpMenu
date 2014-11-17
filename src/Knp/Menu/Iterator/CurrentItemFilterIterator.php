<?php

namespace Knp\Menu\Iterator;

use Knp\Menu\Matcher\MatcherInterface;

/**
 * Filter iterator keeping only current items
 *
 * @package Knp\Menu\Iterator
 */
class CurrentItemFilterIterator extends \FilterIterator
{
    /**
     * @var MatcherInterface
     */
    private $matcher;

    /**
     * @param \Iterator        $iterator
     * @param MatcherInterface $matcher
     */
    public function __construct(\Iterator $iterator, MatcherInterface $matcher)
    {
        $this->matcher = $matcher;

        parent::__construct($iterator);
    }

    /**
     * @return bool
     */
    public function accept()
    {
        return $this->matcher->isCurrent($this->current());
    }
}
