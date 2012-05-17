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
     * @expectedException InvalidArgumentException
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

    public function testToArrayWithChildren()
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', array('uri' => 'http://php.net', 'linkAttributes' => array('title' => 'php'), 'display' => false))
            ->addChild('john')
        ;
        $menu->addChild('joe', array('attributes' => array('class' => 'leaf'), 'label' => 'test', 'labelAttributes' => array('class' => 'center'), 'displayChildren' => false));

        $this->assertEquals(
            array(
                'name' => 'test_menu',
                'label' => null,
                'uri' => 'homepage',
                'attributes' => array(),
                'labelAttributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
                'extras' => array(),
                'display' => true,
                'displayChildren' => true,
                'children' => array(
                    'jack' => array(
                        'name' => 'jack',
                        'label' => null,
                        'uri' => 'http://php.net',
                        'attributes' => array(),
                        'labelAttributes' => array(),
                        'linkAttributes' => array('title' => 'php'),
                        'childrenAttributes' => array(),
                        'extras' => array(),
                        'display' => false,
                        'displayChildren' => true,
                        'children' => array(
                            'john' => array(
                                'name' => 'john',
                                'label' => null,
                                'uri' => null,
                                'attributes' => array(),
                                'labelAttributes' => array(),
                                'linkAttributes' => array(),
                                'childrenAttributes' => array(),
                                'extras' => array(),
                                'display' => true,
                                'displayChildren' => true,
                                'children' => array(),
                            ),
                        ),
                    ),
                    'joe' => array(
                        'name' => 'joe',
                        'label' => 'test',
                        'uri' => null,
                        'attributes' => array('class' => 'leaf'),
                        'labelAttributes' => array('class' => 'center'),
                        'linkAttributes' => array(),
                        'childrenAttributes' => array(),
                        'extras' => array(),
                        'display' => true,
                        'displayChildren' => false,
                        'children' => array(),
                    ),
                ),
            ),
            $menu->toArray()
        );
    }

    public function testToArrayWithLimitedChildren()
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', array('uri' => 'http://php.net', 'linkAttributes' => array('title' => 'php'), 'display' => false))
            ->addChild('john')
        ;
        $menu->addChild('joe', array('attributes' => array('class' => 'leaf'), 'label' => 'test', 'labelAttributes' => array('class' => 'center'), 'displayChildren' => false));

        $this->assertEquals(
            array(
                'name' => 'test_menu',
                'label' => null,
                'uri' => 'homepage',
                'attributes' => array(),
                'labelAttributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
                'extras' => array(),
                'display' => true,
                'displayChildren' => true,
                'children' => array(
                    'jack' => array(
                        'name' => 'jack',
                        'label' => null,
                        'uri' => 'http://php.net',
                        'attributes' => array(),
                        'labelAttributes' => array(),
                        'linkAttributes' => array('title' => 'php'),
                        'childrenAttributes' => array(),
                        'extras' => array(),
                        'display' => false,
                        'displayChildren' => true,
                    ),
                    'joe' => array(
                        'name' => 'joe',
                        'label' => 'test',
                        'uri' => null,
                        'attributes' => array('class' => 'leaf'),
                        'labelAttributes' => array('class' => 'center'),
                        'linkAttributes' => array(),
                        'childrenAttributes' => array(),
                        'extras' => array(),
                        'display' => true,
                        'displayChildren' => false,
                    ),
                ),
            ),
            $menu->toArray(1)
        );
    }

    public function testToArrayWithoutChildren()
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', array('uri' => 'http://php.net', 'linkAttributes' => array('title' => 'php'), 'display' => false));
        $menu->addChild('joe', array('attributes' => array('class' => 'leaf'), 'label' => 'test', 'labelAttributes' => array('class' => 'center'), 'displayChildren' => false));

        $this->assertEquals(
            array(
                'name' => 'test_menu',
                'label' => null,
                'uri' => 'homepage',
                'attributes' => array(),
                'labelAttributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
                'extras' => array(),
                'display' => true,
                'displayChildren' => true,
            ),
            $menu->toArray(0)
        );
    }

    public function testCallRecursively()
    {
        $menu = $this->createMenu();
        $child1 = $this->getMock('Knp\Menu\ItemInterface');
        $child1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Child 1'))
        ;
        $child1->expects($this->once())
            ->method('callRecursively')
            ->with('setDisplay', array(false))
        ;
        $menu->addChild($child1);
        $child2 = $this->getMock('Knp\Menu\ItemInterface');
        $child2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Child 2'))
        ;
        $child2->expects($this->once())
            ->method('callRecursively')
            ->with('setDisplay', array(false))
        ;
        $menu->addChild($child2);

        $menu->callRecursively('setDisplay', array(false));
        $this->assertFalse($menu->isDisplayed());
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
     * @param array $attributes
     * @return \Knp\Menu\MenuItem
     */
    protected function createMenu($name = 'test_menu', $uri = 'homepage', array $attributes = array())
    {
        $factory = new MenuFactory();

        return $factory->createItem($name, array('attributes' => $attributes, 'uri' => $uri));
    }
}
