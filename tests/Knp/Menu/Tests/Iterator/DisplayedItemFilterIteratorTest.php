<?php

namespace Knp\Menu\Tests\Iterator;

use Knp\Menu\Iterator\DisplayedItemFilterIterator;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Knp\Menu\Tests\MenuTestCase;

final class DisplayedItemFilterIteratorTest extends MenuTestCase
{
    public function testFiltering(): void
    {
        $ch1 = $this->ch1;
        $this->assertNotNull($ch1);
        $ch2 = $this->ch2;
        $this->assertNotNull($ch2);
        $ch4 = $this->ch4;
        $this->assertNotNull($ch4);
        $menu = $this->menu;
        $this->assertNotNull($menu);
        $ch1->setDisplay(false);
        $ch2->setDisplay(false);
        $ch4->setDisplayChildren(false);

        $names = [];
        $iterator = new \RecursiveIteratorIterator(
            new DisplayedItemFilterIterator(new RecursiveItemIterator(new \ArrayIterator($menu->getChildren()))),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Parent 1', 'Child 3', 'Parent 2', 'Child 4'], $names);
    }
}
