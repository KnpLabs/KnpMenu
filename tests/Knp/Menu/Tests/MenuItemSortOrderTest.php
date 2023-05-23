<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;

final class MenuItemSortOrderTest extends TestCase
{
    public function testItemsWithoutSortOrderShouldBeAppendedToKeepCurrentBehavior(): void
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1', ['sortOrder' => 1]);
        $menu->addChild('c2');
        $menu->addChild('c3', ['sortOrder' => 2]);
        $menu->addChild('c4');
        $menu->addChild('c5');
        $menu->addChild('c6', ['sortOrder' => 1]);
        $menu->addChild('c7', ['sortOrder' => 1]);

        $arr = \array_keys($menu->getChildren());
        $this->assertEquals(['c1', 'c6', 'c7', 'c3', 'c2', 'c4', 'c5'], $arr);
    }

    public function testItemsAreAddedInTheCorrectOrder(): void
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1', ['sortOrder' => 2]);
        $menu->addChild('c2', ['sortOrder' => 4]);
        $menu->addChild('c3', ['sortOrder' => 1]);
        $menu->addChild('c4', ['sortOrder' => 3]);

        $arr = \array_keys($menu->getChildren());
        $this->assertEquals(['c3', 'c1', 'c4', 'c2'], $arr);
    }
}
