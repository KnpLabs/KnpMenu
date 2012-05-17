<?php

namespace Knp\Menu\Tests;

use Knp\Menu\Iterator\CurrentItemFilterIterator;
use Knp\Menu\MenuItem;
use Knp\Menu\MenuFactory;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Knp\Menu\MenuItem
     */
    protected $menu;

    /**
     * @var \Knp\Menu\MenuItem
     */
    protected $pt1;

    /**
     * @var \Knp\Menu\MenuItem
     */
    protected $ch1;

    /**
     * @var \Knp\Menu\MenuItem
     */
    protected $ch2;

    /**
     * @var \Knp\Menu\MenuItem
     */
    protected $ch3;

    /**
     * @var \Knp\Menu\MenuItem
     */
    protected $pt2;

    /**
     * @var \Knp\Menu\MenuItem
     */
    protected $ch4;

    /**
     * @var \Knp\Menu\MenuItem
     */
    protected $gc1;

    protected function setUp()
    {
        $factory = new MenuFactory();
        $this->menu = $factory->createItem('Root li', array('attributes' => array('class' => 'root')));
        $this->pt1 = $this->menu->addChild('Parent 1');
        $this->ch1 = $this->pt1->addChild('Child 1');
        $this->ch2 = $this->pt1->addChild('Child 2');

        // add the 3rd child via addChild with an object
        $this->ch3 = new MenuItem('Child 3', $factory);
        $this->pt1->addChild($this->ch3);

        $this->pt2 = $this->menu->addChild('Parent 2');
        $this->ch4 = $this->pt2->addChild('Child 4');
        $this->gc1 = $this->ch4->addChild('Grandchild 1');
    }

    protected function tearDown()
    {
        $this->menu = null;
        $this->pt1 = null;
        $this->ch1 = null;
        $this->ch2 = null;
        $this->ch3 = null;
        $this->pt2 = null;
        $this->ch4 = null;
        $this->gc1 = null;
    }

    // prints a visual representation of our basic testing tree
    protected function printTestTree()
    {
        print('      Menu Structure   '."\n");
        print('               rt      '."\n");
        print('             /    \    '."\n");
        print('          pt1      pt2 '."\n");
        print('        /  | \      |  '."\n");
        print('      ch1 ch2 ch3  ch4 '."\n");
        print('                    |  '."\n");
        print('                   gc1 '."\n");
    }
}
