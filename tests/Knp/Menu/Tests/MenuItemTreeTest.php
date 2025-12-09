<?php

namespace Knp\Menu\Tests;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;

final class TestMenuItem extends MenuItem
{
}

final class MenuItemTreeTest extends MenuTestCase
{
    public function testSampleTreeIntegrity(): void
    {
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $pt2 = $this->pt2;
        $ch4 = $this->ch4;
        $gc1 = $this->gc1;
        $this->assertCount(2, $menu->getChildren());
        $this->assertCount(3, $pt1->getChildren());
        $this->assertCount(1, $pt2->getChildren());
        $this->assertCount(1, $ch4->getChildren());
        $this->assertEquals('Grandchild 1', $gc1->getName());
    }

    public function testGetLevel(): void
    {
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $pt2 = $this->pt2;
        $ch4 = $this->ch4;
        $gc1 = $this->gc1;
        $this->assertEquals(0, $menu->getLevel());
        $this->assertEquals(1, $pt1->getLevel());
        $this->assertEquals(1, $pt2->getLevel());
        $this->assertEquals(2, $ch4->getLevel());
        $this->assertEquals(3, $gc1->getLevel());
    }

    public function testGetRoot(): void
    {
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $gc1 = $this->gc1;
        $this->assertSame($menu, $menu->getRoot());
        $this->assertSame($menu, $pt1->getRoot());
        $this->assertSame($menu, $gc1->getRoot());
    }

    public function testIsRoot(): void
    {
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $ch3 = $this->ch3;
        $this->assertTrue($menu->isRoot());
        $this->assertFalse($pt1->isRoot());
        $this->assertFalse($ch3->isRoot());
    }

    public function testGetParent(): void
    {
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $ch4 = $this->ch4;
        $gc1 = $this->gc1;
        $this->assertNull($menu->getParent());
        $this->assertSame($menu, $pt1->getParent());
        $this->assertSame($ch4, $gc1->getParent());
    }

    public function testMoveSampleMenuToNewRoot(): void
    {
        $newRoot = new TestMenuItem('newRoot', $this->getMockBuilder(FactoryInterface::class)->getMock());
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $newRoot->addChild($menu);

        $this->assertEquals(1, $menu->getLevel());
        $this->assertEquals(2, $pt1->getLevel());

        $this->assertSame($newRoot, $menu->getRoot());
        $this->assertSame($newRoot, $pt1->getRoot());
        $this->assertFalse($menu->isRoot());
        $this->assertTrue($newRoot->isRoot());
        $this->assertSame($newRoot, $menu->getParent());
    }

    public function testIsFirst(): void
    {
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $pt2 = $this->pt2;
        $ch4 = $this->ch4;
        $this->assertFalse($menu->isFirst(), 'The root item is not considered as first');
        $this->assertTrue($pt1->isFirst());
        $this->assertFalse($pt2->isFirst());
        $this->assertTrue($ch4->isFirst());
    }

    public function testActsLikeFirst(): void
    {
        $ch1 = $this->ch1;
        $ch2 = $this->ch2;
        $ch3 = $this->ch3;
        $ch4 = $this->ch4;
        $menu = $this->menu;
        $ch1->setDisplay(false);
        $this->assertFalse($menu->actsLikeFirst(), 'The root item is not considered as first');
        $this->assertFalse($ch1->actsLikeFirst(), 'A hidden item does not acts like first');
        $this->assertTrue($ch2->actsLikeFirst());
        $this->assertFalse($ch3->actsLikeFirst());
        $this->assertTrue($ch4->actsLikeFirst());
    }

    public function testActsLikeFirstWithNoDisplayedItem(): void
    {
        $pt1 = $this->pt1;
        $pt2 = $this->pt2;
        $pt1->setDisplay(false);
        $pt2->setDisplay(false);
        $this->assertFalse($pt1->actsLikeFirst());
        $this->assertFalse($pt2->actsLikeFirst());
    }

    public function testIsLast(): void
    {
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $pt2 = $this->pt2;
        $ch4 = $this->ch4;
        $this->assertFalse($menu->isLast(), 'The root item is not considered as last');
        $this->assertFalse($pt1->isLast());
        $this->assertTrue($pt2->isLast());
        $this->assertTrue($ch4->isLast());
    }

    public function testActsLikeLast(): void
    {
        $ch3 = $this->ch3;
        $menu = $this->menu;
        $ch1 = $this->ch1;
        $ch2 = $this->ch2;
        $ch4 = $this->ch4;
        $ch3->setDisplay(false);
        $this->assertFalse($menu->actsLikeLast(), 'The root item is not considered as last');
        $this->assertFalse($ch1->actsLikeLast());
        $this->assertTrue($ch2->actsLikeLast());
        $this->assertFalse($ch3->actsLikeLast(), 'A hidden item does not acts like last');
        $this->assertTrue($ch4->actsLikeLast());
    }

    public function testActsLikeLastWithNoDisplayedItem(): void
    {
        $pt1 = $this->pt1;
        $pt2 = $this->pt2;
        $pt1->setDisplay(false);
        $pt2->setDisplay(false);
        $this->assertFalse($pt1->actsLikeLast());
        $this->assertFalse($pt2->actsLikeLast());
    }

    public function testArrayAccess(): void
    {
        $menu = $this->menu;
        $menu->addChild('Child Menu');
        $childMenu = $menu['Child Menu'];
        /** @var ItemInterface $childMenu */
        $this->assertEquals('Child Menu', $childMenu->getName());
        $this->assertNull($menu->getChild('Fake'));

        $menu->addChild('New Child', ['label' => 'New Label']);
        $newChild = $menu['New Child'];
        /** @var ItemInterface $newChild */
        $this->assertEquals(MenuItem::class, \get_class($newChild));
        $this->assertEquals('New Child', $newChild->getName());
        $this->assertEquals('New Label', $newChild->getLabel());

        unset($menu['New Child']);
        $this->assertNull($menu['New Child']);
    }

    public function testCountable(): void
    {
        $menu = $this->menu;
        $this->assertCount(2, $menu);

        $menu->addChild('New Child');
        $this->assertCount(3, $menu);

        unset($menu['New Child']);
        $this->assertCount(2, $menu);
    }

    public function testGetChildren(): void
    {
        $ch4 = $this->ch4;
        $gc1 = $this->gc1;
        $children = $ch4->getChildren();
        $this->assertCount(1, $children);
        $this->assertEquals($gc1->getName(), $children['Grandchild 1']->getName());
    }

    public function testGetFirstChild(): void
    {
        $menu = $this->menu;
        $pt1 = $this->pt1;
        $this->assertSame($pt1, $menu->getFirstChild());
        // test for bug in getFirstChild implementation (when internal array pointer is changed getFirstChild returns wrong child)
        foreach ($menu->getChildren() as $c) {
        }
        $this->assertSame($pt1, $menu->getFirstChild());
    }

    public function testGetLastChild(): void
    {
        $menu = $this->menu;
        $pt2 = $this->pt2;
        $this->assertSame($pt2, $menu->getLastChild());
        // test for bug in getFirstChild implementation (when internal array pointer is changed getLastChild returns wrong child)
        foreach ($menu->getChildren() as $c) {
        }
        $this->assertSame($pt2, $menu->getLastChild());
    }

    public function testAddChildDoesNotUSeTheFactoryIfItem(): void
    {
        $factory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $factory->expects($this->never())
            ->method('createItem');
        $menu = new MenuItem('Root li', $factory);
        $menu->addChild(new MenuItem('Child 3', $factory));
    }

    public function testAddChildFailsIfInAnotherMenu(): void
    {
        $this->expectException(\LogicException::class);

        $factory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $menu = new MenuItem('Root li', $factory);
        $child = new MenuItem('Child 3', $factory);
        $menu->addChild($child);

        $menu2 = new MenuItem('Second menu', $factory);
        $menu2->addChild($child);
    }

    public function testGetChild(): void
    {
        $gc1 = $this->gc1;
        $ch4 = $this->ch4;
        $this->assertSame($gc1, $ch4->getChild('Grandchild 1'));
        $this->assertNull($ch4->getChild('nonexistentchild'));
    }

    public function testRemoveChild(): void
    {
        $ch4 = $this->ch4;
        $gc1 = $this->gc1;
        $gc2 = $ch4->addChild('gc2');
        $gc3 = $ch4->addChild('gc3');
        $gc4 = $ch4->addChild('gc4');
        $this->assertCount(4, $ch4);
        $ch4->removeChild('gc4');
        $this->assertCount(3, $ch4);
        $this->assertTrue($gc1->isFirst());
        $this->assertTrue($gc3->isLast());
    }

    public function testRemoveFakeChild(): void
    {
        $menu = $this->menu;
        $menu->removeChild('fake');
        $this->assertCount(2, $menu->getChildren());
    }

    public function testReAddRemovedChild(): void
    {
        $ch4 = $this->ch4;
        $menu = $this->menu;
        $pt2 = $this->pt2;
        $gc2 = $ch4->addChild('gc2');
        $ch4->removeChild('gc2');
        $menu->addChild($gc2);
        $this->assertCount(3, $menu);
        $this->assertTrue($gc2->isLast());
        $this->assertFalse($pt2->isLast());
    }

    public function testUpdateChildAfterRename(): void
    {
        $pt1 = $this->pt1;
        $menu = $this->menu;
        $pt1->setName('Temp name');
        $this->assertSame($pt1, $menu->getChild('Temp name'));
        $this->assertEquals(['Temp name', 'Parent 2'], \array_keys($menu->getChildren()));
        $this->assertNull($menu->getChild('Parent 1'));
    }

    public function testRenameToExistingSiblingNameThrowAnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $pt1 = $this->pt1;
        $pt1->setName('Parent 2');
    }

    public function testGetUri(): void
    {
        $this->addChildWithExternalUrl();
        $pt1 = $this->pt1;
        $menu = $this->menu;
        $this->assertNull($pt1->getUri());
        $this->assertEquals('http://www.symfony-reloaded.org', $menu->getChildren()['child']->getUri());
    }

    protected function addChildWithExternalUrl(): void
    {
        $menu = $this->menu;
        $menu->addChild('child', ['uri' => 'http://www.symfony-reloaded.org']);
    }
}
