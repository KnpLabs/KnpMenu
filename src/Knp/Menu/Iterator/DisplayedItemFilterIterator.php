<?php

namespace Knp\Menu\Iterator;

/**
 * Filter iterator keeping only current items
 */
class DisplayedItemFilterIterator extends \RecursiveFilterIterator
{
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function accept()
    {
        return $this->current()->isDisplayed();
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function hasChildren()
    {
        return $this->current()->getDisplayChildren() && parent::hasChildren();
    }
}
