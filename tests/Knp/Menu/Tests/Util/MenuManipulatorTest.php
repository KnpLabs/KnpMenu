<?php

namespace Knp\Menu\Tests\Util;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Tests\TestCase;
use Knp\Menu\Util\MenuManipulator;

class MenuManipulatorTest extends TestCase
{
    public function testMoveToFirstPosition()
    {
        $menu = new MenuItem('root', new MenuFactory());
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $manipulator = new MenuManipulator();
        $manipulator->moveToFirstPosition($menu['c3']);
        $this->assertEquals(array('c3', 'c1', 'c2', 'c4'), array_keys($menu->getChildren()));
    }

    public function testMoveToLastPosition()
    {
        $menu = new MenuItem('root', new MenuFactory());
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $manipulator = new MenuManipulator();
        $manipulator->moveToLastPosition($menu['c2']);
        $this->assertEquals(array('c1', 'c3', 'c4', 'c2'), array_keys($menu->getChildren()));
    }

    public function testMoveToPosition()
    {
        $menu = new MenuItem('root', new MenuFactory());
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $manipulator = new MenuManipulator();
        $manipulator->moveToPosition($menu['c1'], 2);
        $this->assertEquals(array('c2', 'c3', 'c1', 'c4'), array_keys($menu->getChildren()));
    }

    /**
     * @dataProvider getSliceData
     */
    public function testSlice($offset, $length, $count, $keys)
    {
        $manipulator = new MenuManipulator();
        $sliced = $manipulator->slice($this->pt1, $offset, $length);
        $this->assertCount($count, $sliced);
        $this->assertEquals($keys, array_keys($sliced->getChildren()));
    }

    public function getSliceData()
    {
        $this->setUp();

        return array(
            'numeric offset and numeric length' => array(0, 2, 2, array($this->ch1->getName(), $this->ch2->getName())),
            'numeric offset and no length' => array(0, null, 3, array($this->ch1->getName(), $this->ch2->getName(), $this->ch3->getName())),
            'named offset and no length' => array('Child 2', null, 2, array($this->ch2->getName(), $this->ch3->getName())),
            'child offset and no length' => array($this->ch3, null, 1, array($this->ch3->getName())),
            'numeric offset and named length' => array(0, 'Child 3', 2, array($this->ch1->getName(), $this->ch2->getName())),
            'numeric offset and child length' => array(0, $this->ch3, 2, array($this->ch1->getName(), $this->ch2->getName())),
        );
    }

    /**
     * @dataProvider getSplitData
     */
    public function testSplit($length, $count, $keys)
    {
        $manipulator = new MenuManipulator();
        $splitted = $manipulator->split($this->pt1, $length);
        $this->assertArrayHasKey('primary', $splitted);
        $this->assertArrayHasKey('secondary', $splitted);
        $this->assertCount($count, $splitted['primary']);
        $this->assertCount(3 - $count, $splitted['secondary']);
        $this->assertEquals($keys, array_keys($splitted['primary']->getChildren()));
    }

    public function getSplitData()
    {
        $this->setUp();

        return array(
            'numeric length' => array(1, 1, array($this->ch1->getName())),
            'named length' => array('Child 3', 2, array($this->ch1->getName(), $this->ch2->getName())),
            'child length' => array($this->ch3, 2, array($this->ch1->getName(), $this->ch2->getName())),
        );
    }

    public function testPathAsString()
    {
        $manipulator = new MenuManipulator();
        $this->assertEquals('Root li > Parent 2 > Child 4', $manipulator->getPathAsString($this->ch4), 'Path with default separator');
        $this->assertEquals('Root li / Parent 1 / Child 2', $manipulator->getPathAsString($this->ch2, ' / '), 'Path with custom separator');
    }

    public function testBreadcrumbsArray()
    {
        $manipulator = new MenuManipulator();
        $this->menu->addChild('child', array('uri' => 'http://www.symfony-reloaded.org'));
        $this->menu->addChild('123', array('uri' => 'http://www.symfony-reloaded.org'));

        $this->assertEquals(
            array(array('label' => 'Root li', 'uri' => null, 'item' => $this->menu), array('label' => 'Parent 1', 'uri' => null, 'item' => $this->pt1)),
            $manipulator->getBreadcrumbsArray($this->pt1)
        );
        $this->assertEquals(
            array(array('label' => 'Root li', 'uri' => null, 'item' => $this->menu), array('label' => 'child', 'uri' => 'http://www.symfony-reloaded.org', 'item' => $this->menu['child'])),
            $manipulator->getBreadcrumbsArray($this->menu['child'])
        );
        $this->assertEquals(
            array(
                array('label' => 'Root li', 'uri' => null, 'item' => $this->menu),
                array('label' => 'child', 'uri' => 'http://www.symfony-reloaded.org', 'item' => $this->menu['child']),
                array('label' => 'subitem1', 'uri' => null, 'item' => null),
            ),
            $manipulator->getBreadcrumbsArray($this->menu['child'], 'subitem1')
        );

        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->any())
            ->method('getLabel')
            ->will($this->returnValue('mock'));
        $item->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('foo'));

        $this->assertEquals(
            array(
                array('label' => 'Root li', 'uri' => null, 'item' => $this->menu),
                array('label' => 'child', 'uri' => 'http://www.symfony-reloaded.org', 'item' => $this->menu['child']),
                array('label' => 'subitem1', 'uri' => null, 'item' => null),
                array('label' => 'subitem2', 'uri' => null, 'item' => null),
                array('label' => 'subitem3', 'uri' => 'http://php.net', 'item' => null),
                array('label' => 'subitem4', 'uri' => null, 'item' => null),
                array('label' => 'mock', 'uri' => 'foo', 'item' => $item),
            ),
            $manipulator->getBreadcrumbsArray($this->menu['child'], array(
                'subitem1',
                'subitem2' => null,
                'subitem3' => 'http://php.net',
                array('label' => 'subitem4', 'uri' => null, 'item' => null),
                $item,
            ))
        );

        $this->assertEquals(
            array(array('label' => 'Root li', 'uri' => null, 'item' => $this->menu), array('label' => '123', 'uri' => 'http://www.symfony-reloaded.org', 'item' => $this->menu['123'])),
            $manipulator->getBreadcrumbsArray($this->menu['123'])
        );

        $this->assertEquals(
            array(
                array('label' => 'Root li', 'uri' => null, 'item' => $this->menu),
                array('label' => 'child', 'uri' => 'http://www.symfony-reloaded.org', 'item' => $this->menu['child']),
                array('label' => 'mock', 'uri' => 'foo', 'item' => $item),
            ),
            $manipulator->getBreadcrumbsArray($this->menu['child'], $item)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBreadcrumbsArrayInvalidData()
    {
        $manipulator = new MenuManipulator();
        $manipulator->getBreadcrumbsArray($this->pt1, array(new \stdClass()));
    }

    public function testCallRecursively()
    {
        $factory = new MenuFactory();

        $menu = $factory->createItem('test_menu');

        foreach (range(1, 2) as $i) {
            $child = $this->getMock('Knp\Menu\ItemInterface');
            $child->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('Child '.$i))
            ;
            $child->expects($this->once())
                ->method('setDisplay')
                ->with(false)
            ;
            $child->expects($this->once())
                ->method('getChildren')
                ->will($this->returnValue(array()))
            ;
            $menu->addChild($child);
        }

        $manipulator = new MenuManipulator();

        $manipulator->callRecursively($menu, 'setDisplay', array(false));
        $this->assertFalse($menu->isDisplayed());
    }

    public function testToArrayWithChildren()
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', array('uri' => 'http://php.net', 'linkAttributes' => array('title' => 'php'), 'display' => false))
            ->addChild('john', array('current' => true))->setCurrent(true)
        ;
        $menu->addChild('joe', array(
            'attributes' => array('class' => 'leaf'),
            'label' => 'test',
            'labelAttributes' => array('class' => 'center'),
            'displayChildren' => false,
        ))->setCurrent(false);

        $manipulator = new MenuManipulator();

        $this->assertEquals(
            array(
                'name' => 'test_menu',
                'label' => 'test_menu',
                'uri' => 'homepage',
                'attributes' => array(),
                'labelAttributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
                'extras' => array(),
                'display' => true,
                'displayChildren' => true,
                'current' => null,
                'children' => array(
                    'jack' => array(
                        'name' => 'jack',
                        'label' => 'jack',
                        'uri' => 'http://php.net',
                        'attributes' => array(),
                        'labelAttributes' => array(),
                        'linkAttributes' => array('title' => 'php'),
                        'childrenAttributes' => array(),
                        'extras' => array(),
                        'display' => false,
                        'displayChildren' => true,
                        'current' => null,
                        'children' => array(
                            'john' => array(
                                'name' => 'john',
                                'label' => 'john',
                                'uri' => null,
                                'attributes' => array(),
                                'labelAttributes' => array(),
                                'linkAttributes' => array(),
                                'childrenAttributes' => array(),
                                'extras' => array(),
                                'display' => true,
                                'displayChildren' => true,
                                'children' => array(),
                                'current' => true,
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
                        'current' => false,
                    ),
                ),
            ),
            $manipulator->toArray($menu)
        );
    }

    public function testToArrayWithLimitedChildren()
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', array('uri' => 'http://php.net', 'linkAttributes' => array('title' => 'php'), 'display' => false))
            ->addChild('john')
        ;
        $menu->addChild('joe', array('attributes' => array('class' => 'leaf'), 'label' => 'test', 'labelAttributes' => array('class' => 'center'), 'displayChildren' => false));

        $manipulator = new MenuManipulator();

        $this->assertEquals(
            array(
                'name' => 'test_menu',
                'label' => 'test_menu',
                'uri' => 'homepage',
                'attributes' => array(),
                'labelAttributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
                'extras' => array(),
                'display' => true,
                'displayChildren' => true,
                'current' => null,
                'children' => array(
                    'jack' => array(
                        'name' => 'jack',
                        'label' => 'jack',
                        'uri' => 'http://php.net',
                        'attributes' => array(),
                        'labelAttributes' => array(),
                        'linkAttributes' => array('title' => 'php'),
                        'childrenAttributes' => array(),
                        'extras' => array(),
                        'display' => false,
                        'displayChildren' => true,
                        'current' => null,
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
                        'current' => null,
                    ),
                ),
            ),
            $manipulator->toArray($menu, 1)
        );
    }

    public function testToArrayWithoutChildren()
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', array('uri' => 'http://php.net', 'linkAttributes' => array('title' => 'php'), 'display' => false));
        $menu->addChild('joe', array('attributes' => array('class' => 'leaf'), 'label' => 'test', 'labelAttributes' => array('class' => 'center'), 'displayChildren' => false));

        $manipulator = new MenuManipulator();

        $this->assertEquals(
            array(
                'name' => 'test_menu',
                'label' => 'test_menu',
                'uri' => 'homepage',
                'attributes' => array(),
                'labelAttributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
                'extras' => array(),
                'display' => true,
                'displayChildren' => true,
                'current' => null,
            ),
            $manipulator->toArray($menu, 0)
        );
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
    private function createMenu($name = 'test_menu', $uri = 'homepage', array $attributes = array())
    {
        $factory = new MenuFactory();

        return $factory->createItem($name, array('attributes' => $attributes, 'uri' => $uri));
    }
}
