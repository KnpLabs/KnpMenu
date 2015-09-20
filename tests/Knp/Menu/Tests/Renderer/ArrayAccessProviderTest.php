<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\ArrayAccessProvider;

class ArrayAccessProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $provider = new ArrayAccessProvider(new \ArrayObject(), 'first', array('first' => 'first', 'second' => 'dummy'));
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer()
    {
        $registry = new \Pimple();
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $registry['renderer'] = $renderer;
        $provider = new ArrayAccessProvider($registry, 'default',  array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get('default'));
    }

    public function testGetDefaultRenderer()
    {
        $registry = new \ArrayObject();
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $registry['renderer'] = $renderer;
        $provider = new ArrayAccessProvider($registry, 'default',  array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentRenderer()
    {
        $provider = new ArrayAccessProvider(new \ArrayObject(), 'default', array());
        $provider->get('non-existent');
    }
}
