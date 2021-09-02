<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\ChainProvider;
use Knp\Menu\Provider\MenuProviderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class ChainProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testHas(): void
    {
        $innerProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
        $innerProvider->expects($this->exactly(3))
            ->method('has')
            ->willReturnOnConsecutiveCalls(true, false, false)
        ;
        $provider = new ChainProvider([$innerProvider]);
        $this->assertTrue($provider->has('first'));
        $this->assertFalse($provider->has('second'));
        $this->assertFalse($provider->has('third', ['foo' => 'bar']));
    }

    public function testGetExistentMenu(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $innerProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
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
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $innerProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
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
        $menu = $this->prophesize(ItemInterface::class);

        $innerProvider = $this->prophesize(MenuProviderInterface::class);
        $innerProvider->has('foo', [])->willReturn(true);
        $innerProvider->get('foo', [])->willReturn($menu);

        $provider = new ChainProvider(new \ArrayIterator([$innerProvider->reveal()]));
        $this->assertTrue($provider->has('foo'));
        $this->assertSame($menu->reveal(), $provider->get('foo'));
    }
}
