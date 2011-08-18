<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuItem;

class TestMenuItem extends MenuItem {}

class MenuItemTreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Knp\Menu\MenuItem
     */
    private $menu;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $pt1;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $ch1;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $ch2;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $ch3;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $pt2;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $ch4;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $gc1;

    public function setUp()
    {
        $this->menu = new MenuItem('Root li', null, array('class' => 'root'));
        $this->pt1 = $this->menu->addChild('Parent 1');
        $this->ch1 = $this->pt1->addChild('Child 1');
        $this->ch2 = $this->pt1->addChild('Child 2');

        // add the 3rd child via addChild with an object
        $this->ch3 = new MenuItem('Child 3');
        $this->pt1->addChild($this->ch3);

        $this->pt2 = $this->menu->addChild('Parent 2');
        $this->ch4 = $this->pt2->addChild('Child 4');
        $this->gc1 = $this->ch4->addChild('Grandchild 1');
    }

    public function tearDown()
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

    public function testSampleTreeIntegrity()
    {
        $this->assertEquals(2, count($this->menu));
        $this->assertEquals(3, count($this->menu['Parent 1']));
        $this->assertEquals(1, count($this->menu['Parent 2']));
        $this->assertEquals(1, count($this->menu['Parent 2']['Child 4']));
        $this->assertEquals('Grandchild 1', $this->menu['Parent 2']['Child 4']['Grandchild 1']->getName());
    }

    public function testChildrenHaveParentClass()
    {
        $menu = new TestMenuItem('Root li', null, array('class' => 'root'));
        $pt1 = $menu->addChild('Parent 1');
        $ch1 = $pt1->addChild('Child 1');
        $ch2 = $pt1->addChild('Child 2');

        $this->assertInstanceOf('Knp\Menu\Tests\TestMenuItem', $pt1);
        $this->assertInstanceOf('Knp\Menu\Tests\TestMenuItem', $ch1);
    }

    public function testGetLevel()
    {
        $this->assertEquals(0, $this->menu->getLevel());
        $this->assertEquals(1, $this->pt1->getLevel());
        $this->assertEquals(1, $this->pt2->getLevel());
        $this->assertEquals(2, $this->ch4->getLevel());
        $this->assertEquals(3, $this->gc1->getLevel());
    }

    public function testGetRoot()
    {
        $this->assertSame($this->menu, $this->menu->getRoot());
        $this->assertSame($this->menu, $this->pt1->getRoot());
        $this->assertSame($this->menu, $this->gc1->getRoot());
    }

    public function testIsRoot()
    {
        $this->assertTrue($this->menu->isRoot());
        $this->assertFalse($this->pt1->isRoot());
        $this->assertFalse($this->ch3->isRoot());
    }

    public function testGetParent()
    {
        $this->assertNull($this->menu->getParent());
        $this->assertSame($this->menu, $this->pt1->getParent());
        $this->assertSame($this->ch4, $this->gc1->getParent());
    }

    public function testMoveSampleMenuToNewRoot()
    {
        $newRoot = new TestMenuItem("newRoot");
        $newRoot->addChild($this->menu);

        $this->assertEquals(1, $this->menu->getLevel());
        $this->assertEquals(2, $this->pt1->getLevel());

        $this->assertSame($newRoot, $this->menu->getRoot());
        $this->assertSame($newRoot, $this->pt1->getRoot());
        $this->assertFalse($this->menu->isRoot());
        $this->assertTrue($newRoot->isRoot());
        $this->assertSame($newRoot, $this->menu->getParent());
    }

    public function testIsFirst()
    {
        $this->assertTrue($this->pt1->isFirst());
        $this->assertFalse($this->pt2->isFirst());
        $this->assertTrue($this->ch4->isFirst());
    }

    public function testIsLast()
    {
        $this->assertFalse($this->pt1->isLast());
        $this->assertTrue($this->pt2->isLast());
        $this->assertTrue($this->ch4->isLast());
    }

    public function testArrayAccess()
    {
        $this->menu->addChild('Child Menu');
        $this->assertEquals('Child Menu', $this->menu['Child Menu']->getName());
        $this->assertNull($this->menu['Fake']);

        $this->menu['New Child'] = 'New Label';
        $this->assertEquals('Knp\Menu\MenuItem', get_class($this->menu['New Child']));
        $this->assertEquals('New Child', $this->menu['New Child']->getName());
        $this->assertEquals('New Label', $this->menu['New Child']->getLabel());

        unset($this->menu['New Child']);
        $this->assertNull($this->menu['New Child']);
    }

    public function testCountable()
    {
        $this->assertEquals(2, count($this->menu));

        $this->menu->addChild('New Child');
        $this->assertEquals(3, count($this->menu));

        unset($this->menu['New Child']);
        $this->assertEquals(2, count($this->menu));
    }

    public function testIterator()
    {
        $count = 0;
        foreach ($this->pt1 as $key => $value) {
            $count++;
            $this->assertEquals('Child '.$count, $key);
            $this->assertEquals('Child '.$count, $value->getLabel());
        }
    }

    public function testGetChildren()
    {
        $children = $this->ch4->getChildren();
        $this->assertEquals(1, count($children));
        $this->assertEquals($this->gc1->getName(), $children['Grandchild 1']->getName());
    }

    public function testGetFirstChild()
    {
        $this->assertSame($this->pt1, $this->menu->getFirstChild());
        // test for bug in getFirstChild implementation (when internal array pointer is changed getFirstChild returns wrong child)
        foreach ($this->menu->getChildren() as $c);
        $this->assertSame($this->pt1, $this->menu->getFirstChild());
    }

    public function testGetLastChild()
    {
        $this->assertSame($this->pt2, $this->menu->getLastChild());
        // test for bug in getFirstChild implementation (when internal array pointer is changed getLastChild returns wrong child)
        foreach ($this->menu->getChildren() as $c);
        $this->assertSame($this->pt2, $this->menu->getLastChild());
    }

    public function testAddChild()
    {
        // Setup the tree with a different class
        $menu = new TestMenuItem('Root li', null, array('class' => 'root'));
        $pt1 = $menu->addChild('Parent 1');
        $ch1 = $pt1->addChild('Child 1');
        $ch2 = $pt1->addChild('Child 2');
        $ch3 = new TestMenuItem('Child 3');
        $pt1->addChild($ch3);
        $pt2 = $menu->addChild('Parent 2');
        $ch4 = $pt2->addChild('Child 4');
        $gc1 = $ch4->addChild('Grandchild 1');

        // a) Add a child (gc2) to ch4 via ->addChild().
        $gc2 = $ch4->addChild('gc2');
        $this->assertEquals(2, count($ch4->getChildren()));
        $this->assertEquals('Knp\Menu\Tests\TestMenuItem', get_class($gc2));

        // b) Add another child (temp) to ch4 via ->addChild(), but specify the class.
        $temp = $ch4->addChild('temp', null, array(), 'Knp\Menu\Tests\TestMenuItem');
        $this->assertEquals('Knp\Menu\Tests\TestMenuItem', get_class($temp));
        $ch4->removeChild($temp);

        // c) Add a child (gc3) to ch4 by passing an object to addChild().
        $gc3 = new TestMenuItem('gc3');
        $ch4->addChild($gc3);
        $this->assertEquals(3, count($ch4->getChildren()));

        // d) Try to add gc3 again, should throw an exception.
        try {
            $pt1->addChild($gc3);
            $this->fail();
        } catch (\LogicException $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetChild()
    {
        $this->assertSame($this->gc1, $this->ch4->getChild('Grandchild 1'));
        $this->assertNull($this->ch4->getChild('nonexistentchild'));
    }

    public function testRemoveChild()
    {
        $gc2 = $this->ch4->addChild('gc2');
        $gc3 = $this->ch4->addChild('gc3');
        $gc4 = $this->ch4->addChild('gc4');
        $this->assertEquals(4, count($this->ch4));
        $this->ch4->removeChild('gc4');
        $this->assertEquals(3, count($this->ch4));
        $this->assertTrue($this->ch4->getChild('Grandchild 1')->isFirst());
        $this->assertTrue($this->ch4->getChild('gc3')->isLast());
    }

    public function testRemoveFakeChild()
    {
        $this->menu->removeChild('fake');
        $this->assertEquals(2, count($this->menu));
    }

    public function testReAddRemovedChild()
    {
        $gc2 = $this->ch4->addChild('gc2');
        $this->ch4->removeChild('gc2');
        $this->menu->addChild($gc2);
        $this->assertEquals(3, count($this->menu));
        $this->assertTrue($gc2->isLast());
        $this->assertFalse($this->pt2->isLast());
    }

    public function testUpdateChildAfterRename()
    {
        $this->pt1->setName('Temp name');
        $this->assertSame($this->pt1, $this->menu->getChild('Temp name'));
        $this->assertEquals(array('Temp name', 'Parent 2'), array_keys($this->menu->getChildren()));
        $this->assertNull($this->menu->getChild('Parent 1'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRenameToExistingSiblingNameThrowAnException()
    {
        $this->pt1->setName('Parent 2');
    }

    public function testGetSetCurrentUri()
    {
        $this->addChildWithExternalUrl();
        $this->assertNull($this->menu->getCurrentUri());
        $this->menu->setCurrentUri('http://symfony-reloaded.org/');
        $this->assertEquals('http://symfony-reloaded.org/', $this->menu->getCurrentUri());
        $this->assertEquals('http://symfony-reloaded.org/', $this->menu['child']->getCurrentUri());
    }

    public function testChildrenCurrentUri()
    {
        $this->addChildWithExternalUrl();
        $this->menu->setCurrentUri('http://symfony-reloaded.org/');
        $this->menu->addChild('test_child', 'http://php.net/');
        $this->assertEquals('http://symfony-reloaded.org/', $this->menu['test_child']->getCurrentUri());
    }

    public function testGetIsCurrentWhenCurrentUriIsNotSet()
    {
        $this->addChildWithExternalUrl();
        $this->assertFalse($this->menu['child']->getIsCurrent());
    }

    public function testGetIsCurrentWhenCurrentUriIsSet()
    {
        $this->addChildWithExternalUrl();
        $this->menu->setCurrentUri('http://www.symfony-reloaded.org');
        $this->assertTrue($this->menu['child']->getIsCurrent());
        $this->assertFalse($this->pt1->getIsCurrent());
    }

    public function testGetIsCurrentAncestor()
    {
        $this->addChildWithExternalUrl();
        $this->menu->setCurrentUri('http://php.net');
        $this->pt1->setUri('http://php.net');
        $this->assertFalse($this->pt1->getIsCurrentAncestor());
        $this->assertTrue($this->menu->getIsCurrentAncestor());
    }

    public function testDeepGetIsCurrentAncestor()
    {
        $this->addChildWithExternalUrl();
        $this->menu->setCurrentUri('http://php.net');
        $this->gc1->setUri('http://php.net');
        $this->assertFalse($this->pt1->getIsCurrentAncestor());
        $this->assertTrue($this->menu->getIsCurrentAncestor());
        $this->assertTrue($this->pt2->getIsCurrentAncestor());
        $this->assertTrue($this->ch4->getIsCurrentAncestor());
    }

    public function testGetUri()
    {
        $this->addChildWithExternalUrl();
        $this->assertNull($this->pt1->getUri());
        $this->assertEquals('http://www.symfony-reloaded.org', $this->menu['child']->getUri());
    }

    protected function addChildWithExternalUrl()
    {
        $this->menu->addChild('child', 'http://www.symfony-reloaded.org');
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
