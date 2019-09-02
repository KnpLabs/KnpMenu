<?php

namespace Knp\Menu\Tests\Iterator;

use Knp\Menu\Iterator\DisplayedItemFilterIterator;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Knp\Menu\Tests\MenuTestCase;

final class DisplayedItemFilterIteratorTest extends MenuTestCase
{
    public function testFiltering()
    {
        $this->ch1->setDisplay(false);
        $this->ch2->setDisplay(false);
        $this->ch4->setDisplayChildren(false);

        $names = [];
        $iterator = new \RecursiveIteratorIterator(
            new DisplayedItemFilterIterator(new RecursiveItemIterator($this->menu)),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Parent 1', 'Child 3', 'Parent 2', 'Child 4'], $names);
    }
}
