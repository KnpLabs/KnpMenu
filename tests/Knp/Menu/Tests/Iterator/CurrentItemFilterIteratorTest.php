<?php

namespace Knp\Menu\Tests\Iterator;

use Knp\Menu\Iterator\CurrentItemFilterIterator;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Tests\MenuTestCase;

final class CurrentItemFilterIteratorTest extends MenuTestCase
{
    public function testSimpleFiltering(): void
    {
        $pt1 = $this->pt1;
        $ch2 = $this->ch2;
        $gc1 = $this->gc1;
        $menu = $this->menu;
        $pt1->setCurrent(true);
        $ch2->setCurrent(true);
        $gc1->setCurrent(true);

        $names = [];
        // FilterIterator expects an Iterator implementation explicitly, not an IteratorAggregate.
        $iterator = new CurrentItemFilterIterator(new \IteratorIterator($this->menu), new Matcher());

        foreach ($iterator as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Parent 1'], $names);
    }

    public function testFiltering(): void
    {
        $pt1 = $this->pt1;
        $ch2 = $this->ch2;
        $gc1 = $this->gc1;
        $menu = $this->menu;
        $pt1->setCurrent(true);
        $ch2->setCurrent(true);
        $gc1->setCurrent(true);

        $names = [];
        $iterator = new CurrentItemFilterIterator(
            new \RecursiveIteratorIterator(new RecursiveItemIterator($menu), \RecursiveIteratorIterator::SELF_FIRST),
            new Matcher()
        );

        foreach ($iterator as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Parent 1', 'Child 2', 'Grandchild 1'], $names);
    }
}
