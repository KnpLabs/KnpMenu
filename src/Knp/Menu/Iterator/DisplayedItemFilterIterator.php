<?php

namespace Knp\Menu\Iterator;

/**
 * Filter iterator keeping only current items
 *
 * @package Knp\Menu\Iterator
 */
class DisplayedItemFilterIterator extends \RecursiveFilterIterator
{
    /**
     * @return mixed
     */
    public function accept()
    {
        return $this->current()->isDisplayed();
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->current()->getDisplayChildren() && parent::hasChildren();
    }
}
