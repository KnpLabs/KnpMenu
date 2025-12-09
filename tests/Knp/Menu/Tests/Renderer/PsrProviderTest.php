<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\PsrProvider;
use Knp\Menu\Renderer\RendererInterface;
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

        $provider = new PsrProvider($container, 'first');
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer(): void
    {
        $renderer = $this->createStub(RendererInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('renderer')
            ->willReturn(true);
        $container
            ->method('get')
            ->with('renderer')
            ->willReturn($renderer);

        $provider = new PsrProvider($container, 'default');
        $this->assertSame($renderer, $provider->get('renderer'));
    }

    public function testGetDefaultRenderer(): void
    {
        $renderer = $this->createStub(RendererInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('default')
            ->willReturn(true);
        $container
            ->method('get')
            ->with('default')
            ->willReturn($renderer);

        $provider = new PsrProvider($container, 'default');
        $this->assertSame($renderer, $provider->get());
    }

    public function testGetNonExistentRenderer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with('non-existent')
            ->willReturn(false);

        $provider = new PsrProvider($container, 'default');

        $this->expectException(\InvalidArgumentException::class);

        $provider->get('non-existent');
    }
}
