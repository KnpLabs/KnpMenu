<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\PsrProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;

final class PsrProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testHas(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('first')->willReturn(true);
        $container->has('second')->willReturn(true);
        $container->has('third')->willReturn(false);

        $provider = new PsrProvider($container->reveal());
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu(): void
    {
        $menu = $this->prophesize(ItemInterface::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('menu')->willReturn(true);
        $container->get('menu')->willReturn($menu);

        $provider = new PsrProvider($container->reveal());
        $this->assertSame($menu->reveal(), $provider->get('menu'));
    }

    public function testGetNonExistentMenu(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('non-existent')->willReturn(false);

        $provider = new PsrProvider($container->reveal());
        $provider->get('non-existent');
    }
}
