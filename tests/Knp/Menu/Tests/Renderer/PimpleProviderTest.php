<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\PimpleProvider;

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
        $provider = new PimpleProvider(new \Pimple(), array('first' => 'first', 'second' => 'dummy'));
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
        $provider = new PimpleProvider($pimple, array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get('default'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentRenderer()
    {
        $provider = new PimpleProvider(new \Pimple());
        $provider->get('non-existent');
    }
}
