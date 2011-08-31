<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuFactory;

class MenuFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArrayWithoutChildren()
    {
        $factory = new MenuFactory();
        $array = array(
            'name' => 'joe',
            'uri' => '/foobar',
            'display' => false,
        );
        $item = $factory->createFromArray($array);
        $this->assertEquals('joe', $item->getName());
        $this->assertEquals('/foobar', $item->getUri());
        $this->assertFalse($item->isDisplayed());
        $this->assertEmpty($item->getAttributes());
        $this->assertEmpty($item->getChildren());
    }

    public function testFromArrayWithChildren()
    {
        $factory = new MenuFactory();
        $array = array(
            'name' => 'joe',
            'children' => array(
                'jack' => array(
                    'name' => 'jack',
                    'label' => 'Jack',
                ),
                array(
                    'name' => 'john'
                )
            ),
        );
        $item = $factory->createFromArray($array);
        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertEquals(2, count($item));
        $this->assertTrue(isset($item['john']));
    }
}
