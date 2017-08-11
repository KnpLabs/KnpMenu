<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\PimpleProvider;

/**
 * @group legacy
 */
class PimpleProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Pimple')) {
            $this->markTestSkipped('Pimple is not available');
        }
    }

    public function testHas()
    {
        $provider = new PimpleProvider(new \Pimple(), 'first', array('first' => 'first', 'second' => 'dummy'));
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer()
    {
        $pimple = new \Pimple();
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $pimple['renderer'] = function() use ($renderer) {
            return $renderer;
        };
        $provider = new PimpleProvider($pimple, 'default',  array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get('default'));
    }

    public function testGetDefaultRenderer()
    {
        $pimple = new \Pimple();
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $pimple['renderer'] = function() use ($renderer) {
            return $renderer;
        };
        $provider = new PimpleProvider($pimple, 'default',  array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentRenderer()
    {
        $provider = new PimpleProvider(new \Pimple(), 'default', array());
        $provider->get('non-existent');
    }
}
