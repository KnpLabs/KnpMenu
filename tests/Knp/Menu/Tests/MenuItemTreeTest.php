<?php

namespace Knp\Menu\Tests;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;

final class TestMenuItem extends MenuItem
{
}

final class MenuItemTreeTest extends MenuTestCase
{
    public function testSampleTreeIntegrity(): void
    {
        $this->assertCount(2, $this->menu);
        $this->assertCount(3, $this->menu['Parent 1']);
        $this->assertCount(1, $this->menu['Parent 2']);
        $this->assertCount(1, $this->menu['Parent 2']['Child 4']);
        $this->assertEquals('Grandchild 1', $this->menu['Parent 2']['Child 4']['Grandchild 1']->getName());
    }

    public function testGetLevel(): void
    {
        $this->assertEquals(0, $this->menu->getLevel());
        $this->assertEquals(1, $this->pt1->getLevel());
        $this->assertEquals(1, $this->pt2->getLevel());
        $this->assertEquals(2, $this->ch4->getLevel());
        $this->assertEquals(3, $this->gc1->getLevel());
    }

    public function testGetRoot(): void
    {
        $this->assertSame($this->menu, $this->menu->getRoot());
        $this->assertSame($this->menu, $this->pt1->getRoot());
        $this->assertSame($this->menu, $this->gc1->getRoot());
    }

    public function testIsRoot(): void
    {
        $this->assertTrue($this->menu->isRoot());
        $this->assertFalse($this->pt1->isRoot());
        $this->assertFalse($this->ch3->isRoot());
    }

    public function testGetParent(): void
    {
        $this->assertNull($this->menu->getParent());
        $this->assertSame($this->menu, $this->pt1->getParent());
        $this->assertSame($this->ch4, $this->gc1->getParent());
    }

    public function testMoveSampleMenuToNewRoot(): void
    {
        $newRoot = new TestMenuItem('newRoot', $this->getMockBuilder(FactoryInterface::class)->getMock());
        $newRoot->addChild($this->menu);

        $this->assertEquals(1, $this->menu->getLevel());
        $this->assertEquals(2, $this->pt1->getLevel());

        $this->assertSame($newRoot, $this->menu->getRoot());
        $this->assertSame($newRoot, $this->pt1->getRoot());
        $this->assertFalse($this->menu->isRoot());
        $this->assertTrue($newRoot->isRoot());
        $this->assertSame($newRoot, $this->menu->getParent());
    }

    public function testIsFirst(): void
    {
        $this->assertFalse($this->menu->isFirst(), 'The root item is not considered as first');
        $this->assertTrue($this->pt1->isFirst());
        $this->assertFalse($this->pt2->isFirst());
        $this->assertTrue($this->ch4->isFirst());
    }

    public function testActsLikeFirst(): void
    {
        $this->ch1->setDisplay(false);
        $this->assertFalse($this->menu->actsLikeFirst(), 'The root item is not considered as first');
        $this->assertFalse($this->ch1->actsLikeFirst(), 'A hidden item does not acts like first');
        $this->assertTrue($this->ch2->actsLikeFirst());
        $this->assertFalse($this->ch3->actsLikeFirst());
        $this->assertTrue($this->ch4->actsLikeFirst());
    }

    public function testActsLikeFirstWithNoDisplayedItem(): void
    {
        $this->pt1->setDisplay(false);
        $this->pt2->setDisplay(false);
        $this->assertFalse($this->pt1->actsLikeFirst());
        $this->assertFalse($this->pt2->actsLikeFirst());
    }

    public function testIsLast(): void
    {
        $this->assertFalse($this->menu->isLast(), 'The root item is not considered as last');
        $this->assertFalse($this->pt1->isLast());
        $this->assertTrue($this->pt2->isLast());
        $this->assertTrue($this->ch4->isLast());
    }

    public function testActsLikeLast(): void
    {
        $this->ch3->setDisplay(false);
        $this->assertFalse($this->menu->actsLikeLast(), 'The root item is not considered as last');
        $this->assertFalse($this->ch1->actsLikeLast());
        $this->assertTrue($this->ch2->actsLikeLast());
        $this->assertFalse($this->ch3->actsLikeLast(), 'A hidden item does not acts like last');
        $this->assertTrue($this->ch4->actsLikeLast());
    }

    public function testActsLikeLastWithNoDisplayedItem(): void
    {
        $this->pt1->setDisplay(false);
        $this->pt2->setDisplay(false);
        $this->assertFalse($this->pt1->actsLikeLast());
        $this->assertFalse($this->pt2->actsLikeLast());
    }

    public function testArrayAccess(): void
    {
        $this->menu->addChild('Child Menu');
        $this->assertEquals('Child Menu', $this->menu['Child Menu']->getName());
        $this->assertNull($this->menu['Fake']);

        $this->menu['New Child'] = 'New Label';
        $this->assertEquals(MenuItem::class, \get_class($this->menu['New Child']));
        $this->assertEquals('New Child', $this->menu['New Child']->getName());
        $this->assertEquals('New Label', $this->menu['New Child']->getLabel());

        unset($this->menu['New Child']);
        $this->assertNull($this->menu['New Child']);
    }

    public function testCountable(): void
    {
        $this->assertCount(2, $this->menu);

        $this->menu->addChild('New Child');
        $this->assertCount(3, $this->menu);

        unset($this->menu['New Child']);
        $this->assertCount(2, $this->menu);
    }

    public function testGetChildren(): void
    {
        $children = $this->ch4->getChildren();
        $this->assertCount(1, $children);
        $this->assertEquals($this->gc1->getName(), $children['Grandchild 1']->getName());
    }

    public function testGetFirstChild(): void
    {
        $this->assertSame($this->pt1, $this->menu->getFirstChild());
        // test for bug in getFirstChild implementation (when internal array pointer is changed getFirstChild returns wrong child)
        foreach ($this->menu->getChildren() as $c) {
        }
        $this->assertSame($this->pt1, $this->menu->getFirstChild());
    }

    public function testGetLastChild(): void
    {
        $this->assertSame($this->pt2, $this->menu->getLastChild());
        // test for bug in getFirstChild implementation (when internal array pointer is changed getLastChild returns wrong child)
        foreach ($this->menu->getChildren() as $c) {
        }
        $this->assertSame($this->pt2, $this->menu->getLastChild());
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
        $this->assertSame($this->gc1, $this->ch4->getChild('Grandchild 1'));
        $this->assertNull($this->ch4->getChild('nonexistentchild'));
    }

    public function testRemoveChild(): void
    {
        $gc2 = $this->ch4->addChild('gc2');
        $gc3 = $this->ch4->addChild('gc3');
        $gc4 = $this->ch4->addChild('gc4');
        $this->assertCount(4, $this->ch4);
        $this->ch4->removeChild('gc4');
        $this->assertCount(3, $this->ch4);
        $this->assertTrue($this->ch4->getChild('Grandchild 1')->isFirst());
        $this->assertTrue($this->ch4->getChild('gc3')->isLast());
    }

    public function testRemoveFakeChild(): void
    {
        $this->menu->removeChild('fake');
        $this->assertCount(2, $this->menu);
    }

    public function testReAddRemovedChild(): void
    {
        $gc2 = $this->ch4->addChild('gc2');
        $this->ch4->removeChild('gc2');
        $this->menu->addChild($gc2);
        $this->assertCount(3, $this->menu);
        $this->assertTrue($gc2->isLast());
        $this->assertFalse($this->pt2->isLast());
    }

    public function testUpdateChildAfterRename(): void
    {
        $this->pt1->setName('Temp name');
        $this->assertSame($this->pt1, $this->menu->getChild('Temp name'));
        $this->assertEquals(['Temp name', 'Parent 2'], \array_keys($this->menu->getChildren()));
        $this->assertNull($this->menu->getChild('Parent 1'));
    }

    public function testRenameToExistingSiblingNameThrowAnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->pt1->setName('Parent 2');
    }

    public function testGetUri(): void
    {
        $this->addChildWithExternalUrl();
        $this->assertNull($this->pt1->getUri());
        $this->assertEquals('http://www.symfony-reloaded.org', $this->menu['child']->getUri());
    }

    protected function addChildWithExternalUrl(): void
    {
        $this->menu->addChild('child', ['uri' => 'http://www.symfony-reloaded.org']);
    }
}
