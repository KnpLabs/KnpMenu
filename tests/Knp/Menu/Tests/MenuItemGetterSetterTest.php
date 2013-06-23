<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuItem;
use Knp\Menu\MenuFactory;

class MenuItemGetterSetterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateMenuItemWithEmptyParameter()
    {
        $menu = $this->createMenu();
        $this->assertTrue($menu instanceof MenuItem);
    }

    public function testCreateMenuWithNameAndUri()
    {
        $menu = $this->createMenu('test1', 'other_uri');
        $this->assertEquals('test1', $menu->getName());
        $this->assertEquals('other_uri', $menu->getUri());
    }

    public function testCreateMenuWithTitle()
    {
        $title = 'This is a test item title';
        $menu = $this->createMenu(null, null, array('title' => $title));
        $this->assertEquals($title, $menu->getAttribute('title'));
    }

    public function testName()
    {
        $menu = $this->createMenu();
        $menu->setName('menu name');
        $this->assertEquals('menu name', $menu->getName());
    }

    public function testLabel()
    {
        $menu = $this->createMenu();
        $menu->setLabel('menu label');
        $this->assertEquals('menu label', $menu->getLabel());
    }

    public function testNameIsUsedAsDefaultLabel()
    {
        $menu = $this->createMenu('My Label');
        $this->assertEquals('My Label', $menu->getLabel());
        $menu->setLabel('Other Label');
        $this->assertEquals('Other Label', $menu->getLabel());
    }

    public function testUri()
    {
        $menu = $this->createMenu();
        $menu->setUri('menu_uri');
        $this->assertEquals('menu_uri', $menu->getUri());
    }

    public function testAttributes()
    {
        $attributes = array('class' => 'test_class', 'title' => 'Test title');
        $menu = $this->createMenu();
        $menu->setAttributes($attributes);
        $this->assertEquals($attributes, $menu->getAttributes());
    }

    public function testDefaultAttribute()
    {
        $menu = $this->createMenu(null, null, array('id' => 'test_id'));
        $this->assertEquals('test_id', $menu->getAttribute('id'));
        $this->assertEquals('default_value', $menu->getAttribute('unknown_attribute', 'default_value'));
    }

    public function testLinkAttributes()
    {
        $attributes = array('class' => 'test_class', 'title' => 'Test title');
        $menu = $this->createMenu();
        $menu->setLinkAttributes($attributes);
        $this->assertEquals($attributes, $menu->getLinkAttributes());
    }

    public function testDefaultLinkAttribute()
    {
        $menu = $this->createMenu();
        $menu->setLinkAttribute('class', 'test_class');
        $this->assertEquals('test_class', $menu->getLinkAttribute('class'));
        $this->assertNull($menu->getLinkAttribute('title'));
        $this->assertEquals('foobar', $menu->getLinkAttribute('title', 'foobar'));
    }

    public function testChildrenAttributes()
    {
        $attributes = array('class' => 'test_class', 'title' => 'Test title');
        $menu = $this->createMenu();
        $menu->setChildrenAttributes($attributes);
        $this->assertEquals($attributes, $menu->getChildrenAttributes());
    }

    public function testDefaultChildrenAttribute()
    {
        $menu = $this->createMenu();
        $menu->setChildrenAttribute('class', 'test_class');
        $this->assertEquals('test_class', $menu->getChildrenAttribute('class'));
        $this->assertNull($menu->getChildrenAttribute('title'));
        $this->assertEquals('foobar', $menu->getChildrenAttribute('title', 'foobar'));
    }

    public function testLabelAttributes()
    {
        $attributes = array('class' => 'test_class', 'title' => 'Test title');
        $menu = $this->createMenu();
        $menu->setLabelAttributes($attributes);
        $this->assertEquals($attributes, $menu->getLabelAttributes());
    }

    public function testDefaultLabelAttribute()
    {
        $menu = $this->createMenu();
        $menu->setLabelAttribute('class', 'test_class');
        $this->assertEquals('test_class', $menu->getLabelAttribute('class'));
        $this->assertNull($menu->getLabelAttribute('title'));
        $this->assertEquals('foobar', $menu->getLabelAttribute('title', 'foobar'));
    }

    public function testExtras()
    {
        $extras = array('class' => 'test_class', 'title' => 'Test title');
        $menu = $this->createMenu();
        $menu->setExtras($extras);
        $this->assertEquals($extras, $menu->getExtras());
    }

    public function testDefaultExtras()
    {
        $menu = $this->createMenu();
        $menu->setExtra('class', 'test_class');
        $this->assertEquals('test_class', $menu->getExtra('class'));
        $this->assertNull($menu->getExtra('title'));
        $this->assertEquals('foobar', $menu->getExtra('title', 'foobar'));
    }

    public function testDisplay()
    {
        $menu = $this->createMenu();
        $this->assertEquals(true, $menu->isDisplayed());
        $menu->setDisplay(false);
        $this->assertEquals(false, $menu->isDisplayed());
    }

    public function testShowChildren()
    {
        $menu = $this->createMenu();
        $this->assertEquals(true, $menu->getDisplayChildren());
        $menu->setDisplayChildren(false);
        $this->assertEquals(false, $menu->getDisplayChildren());
    }

    public function testParent()
    {
        $menu = $this->createMenu();
        $child = $this->createMenu('child_menu');
        $this->assertNull($child->getParent());
        $child->setParent($menu);
        $this->assertEquals($menu, $child->getParent());
    }

    public function testChildren()
    {
        $menu = $this->createMenu();
        $child = $this->createMenu('child_menu');
        $menu->setChildren(array($child));
        $this->assertEquals(array($child), $menu->getChildren());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetExistingNameThrowsAnException()
    {
        $menu = $this->createMenu();
        $menu->addChild('jack');
        $menu->addChild('joe');
        $menu->getChild('joe')->setName('jack');
    }

    public function testSetSameName()
    {
        $parent = $this->getMock('Knp\Menu\ItemInterface');
        $parent->expects($this->never())
            ->method('offsetExists');

        $menu = $this->createMenu('my_name');
        $menu->setParent($parent);
        $menu->setName('my_name');
        $this->assertEquals('my_name', $menu->getName());
    }

    public function testFactory()
    {
        $child1 = $this->getMock('Knp\Menu\ItemInterface');
        $factory = $this->getMock('Knp\Menu\FactoryInterface');
        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($child1));

        $menu = $this->createMenu();
        $menu->setFactory($factory);

        $menu->addChild('child1');
    }

    /**
     * Create a new MenuItem
     *
     * @param string $name
     * @param string $uri
     * @param array  $attributes
     *
     * @return \Knp\Menu\MenuItem
     */
    protected function createMenu($name = 'test_menu', $uri = 'homepage', array $attributes = array())
    {
        $factory = new MenuFactory();

        return $factory->createItem($name, array('attributes' => $attributes, 'uri' => $uri));
    }
}
