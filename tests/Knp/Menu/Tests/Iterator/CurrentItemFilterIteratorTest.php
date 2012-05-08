<?php

namespace Knp\Menu\Tests\Iterator;

use Knp\Menu\Iterator\CurrentItemFilterIterator;
use Knp\Menu\Iterator\ItemIterator;
use Knp\Menu\Tests\TestCase;

class CurrentItemFilterIteratorTest extends TestCase
{
    public function testFiltering()
    {
        $this->pt1->setCurrent(true);
        $this->ch2->setCurrent(true);
        $this->gc1->setCurrent(true);

        $names = array();
        $iterator = new CurrentItemFilterIterator(
            new \RecursiveIteratorIterator(new ItemIterator($this->menu), \RecursiveIteratorIterator::SELF_FIRST)
        );
        foreach ($iterator as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(array('Parent 1', 'Child 2', 'Grandchild 1'), $names);
    }
}
