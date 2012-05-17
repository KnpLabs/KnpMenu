<?php

namespace Knp\Menu\Iterator;

/**
 * Filter iterator keeping only current items
 */
class DisplayedItemFilterIterator extends \RecursiveFilterIterator
{
    public function accept()
    {
        return $this->current()->isDisplayed();
    }

    public function hasChildren()
    {
        return $this->current()->getDisplayChildren() && parent::hasChildren();
    }
}
