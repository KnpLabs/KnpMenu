<?php

namespace Knp\Menu\Tests\Iterator;

use Knp\Menu\Iterator\CurrentItemFilterIterator;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Tests\MenuTestCase;

final class CurrentItemFilterIteratorTest extends MenuTestCase
{
    public function testSimpleFiltering()
    {
        $this->pt1->setCurrent(true);
        $this->ch2->setCurrent(true);
        $this->gc1->setCurrent(true);

        $names = [];
        // FilterIterator expects an Iterator implementation explicitly, not an IteratorAggregate.
        $iterator = new CurrentItemFilterIterator($this->menu->getIterator(), new Matcher());

        foreach ($iterator as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Parent 1'], $names);
    }

    public function testFiltering()
    {
        $this->pt1->setCurrent(true);
        $this->ch2->setCurrent(true);
        $this->gc1->setCurrent(true);

        $names = [];
        $iterator = new CurrentItemFilterIterator(
            new \RecursiveIteratorIterator(new RecursiveItemIterator($this->menu), \RecursiveIteratorIterator::SELF_FIRST),
            new Matcher()
        );

        foreach ($iterator as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Parent 1', 'Child 2', 'Grandchild 1'], $names);
    }
}
