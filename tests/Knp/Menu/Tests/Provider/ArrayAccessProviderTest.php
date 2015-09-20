<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\Provider\ArrayAccessProvider;

class ArrayAccessProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $provider = new ArrayAccessProvider(new \ArrayObject(), array('first' => 'first', 'second' => 'dummy'));
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu()
    {
        $registry = new \ArrayObject();
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $registry['menu'] = $menu;
        $provider = new ArrayAccessProvider($registry, array('default' => 'menu'));
        $this->assertSame($menu, $provider->get('default'));
    }

    public function testGetMenuAsClosure()
    {
        $registry = new \ArrayObject();
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $registry['menu'] = function ($options, $c) use ($menu) {
            $c['options'] = $options;

            return $menu;
        };
        $provider = new ArrayAccessProvider($registry, array('default' => 'menu'));

        $this->assertSame($menu, $provider->get('default', array('foo' => 'bar')));
        $this->assertEquals(array('foo' => 'bar'), $registry['options']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new ArrayAccessProvider(new \ArrayObject());
        $provider->get('non-existent');
    }
}
