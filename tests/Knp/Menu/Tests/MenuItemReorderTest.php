<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuItem;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;

class MenuItemReorderTest extends TestCase
{
    public function testReordering(): void
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $menu->reorderChildren(['c4', 'c3', 'c2', 'c1']);
        $arr = array_keys($menu->getChildren());
        $this->assertEquals(['c4', 'c3', 'c2', 'c1'], $arr);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReorderingWithTooManyItemNames(): void
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1');
        $menu->reorderChildren(['c1', 'c3']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReorderingWithWrongItemNames(): void
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->reorderChildren(['c1', 'c3']);
    }
}
