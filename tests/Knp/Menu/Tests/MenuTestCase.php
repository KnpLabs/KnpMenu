<?php

namespace Knp\Menu\Tests;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;

abstract class MenuTestCase extends TestCase
{
    protected ItemInterface|null $menu;

    protected ItemInterface|null $pt1;

    protected ItemInterface|null $ch1;

    protected ItemInterface|null $ch2;

    protected ItemInterface|null $ch3;

    protected ItemInterface|null $pt2;

    protected ItemInterface|null $ch4;

    protected ItemInterface|null $gc1;

    protected function setUp(): void
    {
        $factory = new MenuFactory();
        $this->menu = $factory->createItem('Root li', ['childrenAttributes' => ['class' => 'root']]);
        $this->pt1 = $this->menu->addChild('Parent 1');
        $this->ch1 = $this->pt1->addChild('Child 1');
        $this->ch2 = $this->pt1->addChild('Child 2');

        // add the 3rd child via addChild with an object
        $this->ch3 = new MenuItem('Child 3', $factory);
        $this->pt1->addChild($this->ch3);

        $this->pt2 = $this->menu->addChild('Parent 2');
        $this->ch4 = $this->pt2->addChild('Child 4');
        $this->gc1 = new MenuItem('Grandchild 1', $factory);
        $this->ch4->setChildren([$this->gc1]);
    }

    protected function tearDown(): void
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
    protected function printTestTree(): void
    {
        echo '      Menu Structure   '."\n";
        echo '               rt      '."\n";
        echo '             /    \    '."\n";
        echo '          pt1      pt2 '."\n";
        echo '        /  | \      |  '."\n";
        echo '      ch1 ch2 ch3  ch4 '."\n";
        echo '                    |  '."\n";
        echo '                   gc1 '."\n";
    }
}
