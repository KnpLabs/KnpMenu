<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\ArrayAccessProvider;
use PHPUnit\Framework\TestCase;

final class ArrayAccessProviderTest extends TestCase
{
    public function testHas()
    {
        $provider = new ArrayAccessProvider(new \ArrayObject(), 'first', ['first' => 'first', 'second' => 'dummy']);
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer()
    {
        $registry = new \ArrayObject();
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $registry['renderer'] = $renderer;
        $provider = new ArrayAccessProvider($registry, 'default', ['default' => 'renderer']);
        $this->assertSame($renderer, $provider->get('default'));
    }

    public function testGetDefaultRenderer()
    {
        $registry = new \ArrayObject();
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $registry['renderer'] = $renderer;
        $provider = new ArrayAccessProvider($registry, 'default', ['default' => 'renderer']);
        $this->assertSame($renderer, $provider->get());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentRenderer()
    {
        $provider = new ArrayAccessProvider(new \ArrayObject(), 'default', []);
        $provider->get('non-existent');
    }
}
