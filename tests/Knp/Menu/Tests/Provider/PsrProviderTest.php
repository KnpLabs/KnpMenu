<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\PsrProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class PsrProviderTest extends TestCase
{
    public function testHas(): void
    {
        $container = $this->createStub(ContainerInterface::class);
        $container
            ->method('has')
            ->willReturnMap([
                ['first', true],
                ['second', true],
                ['third', false],
            ]);

        $provider = new PsrProvider($container);
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu(): void
    {
        $menu = $this->createStub(ItemInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('menu')
            ->willReturn(true);
        $container
            ->method('get')
            ->with('menu')
            ->willReturn($menu);

        $provider = new PsrProvider($container);
        $this->assertSame($menu, $provider->get('menu'));
    }

    public function testGetNonExistentMenu(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('non-existent')
            ->willReturn(false);

        $provider = new PsrProvider($container);

        $this->expectException(\InvalidArgumentException::class);

        $provider->get('non-existent');
    }
}
