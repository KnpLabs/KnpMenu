<?php

namespace Knp\Menu\Tests\Loader;

use Knp\Menu\Loader\ArrayLoader;
use Knp\Menu\MenuFactory;

class ArrayLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithoutChildren()
    {
        $array = array(
            'name' => 'joe',
            'uri' => '/foobar',
            'display' => false,
        );

        $loader = new ArrayLoader(new MenuFactory());
        $item = $loader->load($array);

        $this->assertEquals('joe', $item->getName());
        $this->assertEquals('/foobar', $item->getUri());
        $this->assertFalse($item->isDisplayed());
        $this->assertEmpty($item->getAttributes());
        $this->assertEmpty($item->getChildren());
    }

    public function testLoadWithChildren()
    {
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

        $loader = new ArrayLoader(new MenuFactory());
        $item = $loader->load($array);

        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertCount(2, $item);
        $this->assertTrue(isset($item['john']));
    }

    public function testLoadWithChildrenOmittingName()
    {
        $array = array(
            'name' => 'joe',
            'children' => array(
                'jack' => array(
                    'label' => 'Jack',
                ),
                'john' => array(
                    'label' => 'John'
                )
            ),
        );

        $loader = new ArrayLoader(new MenuFactory());
        $item = $loader->load($array);

        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertCount(2, $item);
        $this->assertTrue(isset($item['john']));
        $this->assertTrue(isset($item['jack']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadInvalidData()
    {
        $loader = new ArrayLoader(new MenuFactory());

        $loader->load(new \stdClass());
    }

    /**
     * @dataProvider provideSupportingData
     */
    public function testSupports($data, $expected)
    {
        $loader = new ArrayLoader(new MenuFactory());

        $this->assertSame($expected, $loader->supports($data));
    }

    public function provideSupportingData()
    {
        return array(
            array(array(), true),
            array(null, false),
            array('foobar', false),
            array(new \stdClass(), false),
            array(53, false),
            array(true, false),
        );
    }
}
