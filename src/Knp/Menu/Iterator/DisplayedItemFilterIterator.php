<?php

namespace Knp\Menu\Iterator;

/**
 * Filter iterator keeping only current items
 */
class DisplayedItemFilterIterator extends \RecursiveFilterIterator
{
    #[\ReturnTypeWillChange]
    public function accept()
    {
        return $this->current()->isDisplayed();
    }

    #[\ReturnTypeWillChange]
    public function hasChildren()
    {
        return $this->current()->getDisplayChildren() && parent::hasChildren();
    }
}
