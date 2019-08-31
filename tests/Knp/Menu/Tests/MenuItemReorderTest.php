<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;

final class MenuItemReorderTest extends TestCase
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
        $arr = \array_keys($menu->getChildren());
        $this->assertEquals(['c4', 'c3', 'c2', 'c1'], $arr);
    }

    public function testReorderingWithTooManyItemNames(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1');
        $menu->reorderChildren(['c1', 'c3']);
    }

    public function testReorderingWithWrongItemNames(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->reorderChildren(['c1', 'c3']);
    }
}
