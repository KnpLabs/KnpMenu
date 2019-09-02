<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\PsrProvider;
use PHPUnit\Framework\TestCase;

final class PsrProviderTest extends TestCase
{
    public function testHas()
    {
        $container = $this->prophesize('Psr\Container\ContainerInterface');
        $container->has('first')->willReturn(true);
        $container->has('second')->willReturn(true);
        $container->has('third')->willReturn(false);

        $provider = new PsrProvider($container->reveal(), 'first');
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer()
    {
        $renderer = $this->prophesize('Knp\Menu\Renderer\RendererInterface');

        $container = $this->prophesize('Psr\Container\ContainerInterface');
        $container->has('renderer')->willReturn(true);
        $container->get('renderer')->willReturn($renderer);

        $provider = new PsrProvider($container->reveal(), 'default');
        $this->assertSame($renderer->reveal(), $provider->get('renderer'));
    }

    public function testGetDefaultRenderer()
    {
        $renderer = $this->prophesize('Knp\Menu\Renderer\RendererInterface');

        $container = $this->prophesize('Psr\Container\ContainerInterface');
        $container->has('default')->willReturn(true);
        $container->get('default')->willReturn($renderer);

        $provider = new PsrProvider($container->reveal(), 'default');
        $this->assertSame($renderer->reveal(), $provider->get());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentRenderer()
    {
        $container = $this->prophesize('Psr\Container\ContainerInterface');
        $container->has('non-existent')->willReturn(false);

        $provider = new PsrProvider($container->reveal(), 'default');
        $provider->get('non-existent');
    }
}
