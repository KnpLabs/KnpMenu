<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\ArrayAccessProvider;
use Knp\Menu\Renderer\RendererInterface;
use PHPUnit\Framework\TestCase;

final class ArrayAccessProviderTest extends TestCase
{
    public function testHas(): void
    {
        $provider = new ArrayAccessProvider(new \ArrayObject(), 'first', ['first' => 'first', 'second' => 'dummy']);
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer(): void
    {
        $registry = new \ArrayObject();
        $renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $registry['renderer'] = $renderer;
        $provider = new ArrayAccessProvider($registry, 'default', ['default' => 'renderer']);
        $this->assertSame($renderer, $provider->get('default'));
    }

    public function testGetDefaultRenderer(): void
    {
        $registry = new \ArrayObject();
        $renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $registry['renderer'] = $renderer;
        $provider = new ArrayAccessProvider($registry, 'default', ['default' => 'renderer']);
        $this->assertSame($renderer, $provider->get());
    }

    public function testGetNonExistentRenderer(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $provider = new ArrayAccessProvider(new \ArrayObject(), 'default', []);
        $provider->get('non-existent');
    }
}
