<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\Provider\ChainProvider;
use PHPUnit\Framework\TestCase;

final class ChainProviderTest extends TestCase
{
    public function testHas(): void
    {
        $innerProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $innerProvider->expects($this->at(0))
            ->method('has')
            ->with('first')
            ->willReturn(true)
        ;
        $innerProvider->expects($this->at(1))
            ->method('has')
            ->with('second')
            ->willReturn(false)
        ;
        $innerProvider->expects($this->at(2))
            ->method('has')
            ->with('third', ['foo' => 'bar'])
            ->willReturn(false)
        ;
        $provider = new ChainProvider([$innerProvider]);
        $this->assertTrue($provider->has('first'));
        $this->assertFalse($provider->has('second'));
        $this->assertFalse($provider->has('third', ['foo' => 'bar']));
    }

    public function testGetExistentMenu(): void
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $innerProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $innerProvider->expects($this->any())
            ->method('has')
            ->with('default')
            ->willReturn(true)
        ;
        $innerProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($menu)
        ;

        $provider = new ChainProvider([$innerProvider]);
        $this->assertSame($menu, $provider->get('default'));
    }

    public function testGetWithOptions(): void
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $innerProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $innerProvider->expects($this->any())
            ->method('has')
            ->with('default', ['foo' => 'bar'])
            ->willReturn(true)
        ;
        $innerProvider->expects($this->once())
            ->method('get')
            ->with('default', ['foo' => 'bar'])
            ->willReturn($menu)
        ;

        $provider = new ChainProvider([$innerProvider]);
        $this->assertSame($menu, $provider->get('default', ['foo' => 'bar']));
    }

    public function testGetNonExistentMenu(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $provider = new ChainProvider([]);
        $provider->get('non-existent');
    }

    public function testIterator(): void
    {
        $menu = $this->prophesize('Knp\Menu\ItemInterface');

        $innerProvider = $this->prophesize('Knp\Menu\Provider\MenuProviderInterface');
        $innerProvider->has('foo', [])->willReturn(true);
        $innerProvider->get('foo', [])->willReturn($menu);

        $provider = new ChainProvider(new \ArrayIterator([$innerProvider->reveal()]));
        $this->assertTrue($provider->has('foo'));
        $this->assertSame($menu->reveal(), $provider->get('foo'));
    }
}
