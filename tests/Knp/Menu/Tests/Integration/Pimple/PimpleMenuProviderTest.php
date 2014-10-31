<?php

namespace Knp\Menu\Tests\Integration\Pimple;

use Knp\Menu\Integration\Pimple\PimpleMenuProvider;

class PimpleMenuProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Pimple')) {
            $this->markTestSkipped('Pimple is not available');
        }
    }

    public function testHas()
    {
        $provider = new PimpleMenuProvider(new \Pimple(), array('first' => 'first', 'second' => 'dummy'));
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu()
    {
        $pimple = new \Pimple();
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $pimple['menu'] = function() use ($menu) {
            return $menu;
        };
        $provider = new PimpleMenuProvider($pimple, array('default' => 'menu'));
        $this->assertSame($menu, $provider->get('default'));
    }

    public function testGetMenuAsClosure()
    {
        $pimple = new \Pimple();
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $pimple['menu'] = $pimple->protect(function($options, $c) use ($menu) {
            $c['options'] = $options;

            return $menu;
        });
        $provider = new PimpleMenuProvider($pimple, array('default' => 'menu'));

        $this->assertSame($menu, $provider->get('default', array('foo' => 'bar')));
        $this->assertEquals(array('foo' => 'bar'), $pimple['options']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new PimpleMenuProvider(new \Pimple());
        $provider->get('non-existent');
    }
}
