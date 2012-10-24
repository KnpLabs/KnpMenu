<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuItem;
use Knp\Menu\MenuFactory;

class MenuItemReorderTest extends \PHPUnit_Framework_TestCase
{
    public function testReordering()
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $menu->reorderChildren(array('c4', 'c3', 'c2', 'c1'));
        $arr = array_keys($menu->getChildren());
        $this->assertEquals(array('c4', 'c3', 'c2', 'c1'), $arr);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReorderingWithTooManyItemNames()
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1');
        $menu->reorderChildren(array('c1', 'c3'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReorderingWithWrongItemNames()
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->reorderChildren(array('c1', 'c3'));
    }
}
