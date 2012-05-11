<?php

namespace Knp\Menu\Iterator;

/**
 * Filter iterator keeping only current items
 */
class CurrentItemFilterIterator extends \FilterIterator
{
    public function accept()
    {
        return $this->current()->isCurrent();
    }
}
