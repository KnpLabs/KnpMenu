<?php

namespace Knp\Menu\Tests\Integration\Pimple;

use Knp\Menu\Integration\Pimple\PimpleRendererProvider;

class PimpleRendererProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Pimple')) {
            $this->markTestSkipped('Pimple is not available');
        }
    }

    public function testHas()
    {
        $provider = new PimpleRendererProvider(new \Pimple(), 'first', array('first' => 'first', 'second' => 'dummy'));
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer()
    {
        $pimple = new \Pimple();
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $pimple['renderer'] = function() use ($renderer) {
            return $renderer;
        };
        $provider = new PimpleRendererProvider($pimple, 'default',  array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get('default'));
    }

    public function testGetDefaultRenderer()
    {
        $pimple = new \Pimple();
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $pimple['renderer'] = function() use ($renderer) {
            return $renderer;
        };
        $provider = new PimpleRendererProvider($pimple, 'default',  array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentRenderer()
    {
        $provider = new PimpleRendererProvider(new \Pimple(), 'default', array());
        $provider->get('non-existent');
    }
}
