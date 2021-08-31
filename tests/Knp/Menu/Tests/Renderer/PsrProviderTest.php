<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\PsrProvider;
use Knp\Menu\Renderer\RendererInterface;
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

        $provider = new PsrProvider($container->reveal(), 'first');
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer(): void
    {
        $renderer = $this->prophesize(RendererInterface::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('renderer')->willReturn(true);
        $container->get('renderer')->willReturn($renderer);

        $provider = new PsrProvider($container->reveal(), 'default');
        $this->assertSame($renderer->reveal(), $provider->get('renderer'));
    }

    public function testGetDefaultRenderer(): void
    {
        $renderer = $this->prophesize(RendererInterface::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('default')->willReturn(true);
        $container->get('default')->willReturn($renderer);

        $provider = new PsrProvider($container->reveal(), 'default');
        $this->assertSame($renderer->reveal(), $provider->get());
    }

    public function testGetNonExistentRenderer(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('non-existent')->willReturn(false);

        $provider = new PsrProvider($container->reveal(), 'default');
        $provider->get('non-existent');
    }
}
