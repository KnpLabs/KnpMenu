<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\ChainProvider;
use Knp\Menu\Provider\MenuProviderInterface;
use PHPUnit\Framework\TestCase;

final class ChainProviderTest extends TestCase
{
    public function testHas(): void
    {
        $innerProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
        $innerProvider
            ->method('has')
            ->withConsecutive(
                ['first'],
                ['second'],
                ['third', ['foo' => 'bar']],
            )
            ->willReturnOnConsecutiveCalls(
                true,
                false,
                false,
            );

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
        $menu = $this->createStub(ItemInterface::class);

        $innerProvider = $this->createMock(MenuProviderInterface::class);
        $innerProvider
            ->method('has')
            ->with('foo', [])
            ->willReturn(true);

        $innerProvider
            ->method('get')
            ->with('foo', [])
            ->willReturn($menu);

        $provider = new ChainProvider(new \ArrayIterator([$innerProvider]));
        $this->assertTrue($provider->has('foo'));
        $this->assertSame($menu, $provider->get('foo'));
    }
}
