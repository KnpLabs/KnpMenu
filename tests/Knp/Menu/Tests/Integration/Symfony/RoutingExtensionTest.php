<?php

namespace Knp\Menu\Tests\Integration\Symfony;

use Knp\Menu\Integration\Symfony\RoutingExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RoutingExtensionTest extends TestCase
{
    public function testCreateItemWithRoute(): void
    {
        $generator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/foobar')
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(['uri' => '/hello', 'route' => 'homepage', 'label' => 'foo']);

        $this->assertEquals('/foobar', $processedOptions['uri']);
        $this->assertEquals('foo', $processedOptions['label']);
    }

    public function testCreateItemWithRouteAndParameters(): void
    {
        $generator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', ['id' => 12], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/foobar')
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(['route' => 'homepage', 'routeParameters' => ['id' => 12]]);

        $this->assertEquals('/foobar', $processedOptions['uri']);
    }

    public function testCreateItemWithAbsoluteRoute(): void
    {
        $generator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://php.net')
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(['route' => 'homepage', 'routeAbsolute' => true]);

        $this->assertEquals('http://php.net', $processedOptions['uri']);
    }

    public function testCreateItemAppendsRouteUnderExtras(): void
    {
        $generator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(['route' => 'homepage']);
        $this->assertEquals([['route' => 'homepage', 'parameters' => []]], $processedOptions['extras']['routes']);

        $processedOptions = $extension->buildOptions(['route' => 'homepage', 'routeParameters' => ['bar' => 'baz']]);
        $this->assertEquals([['route' => 'homepage', 'parameters' => ['bar' => 'baz']]], $processedOptions['extras']['routes']);

        $processedOptions = $extension->buildOptions(['route' => 'homepage', 'extras' => ['routes' => ['other_page']]]);
        $this->assertContains(['route' => 'homepage', 'parameters' => []], $processedOptions['extras']['routes']);
        $this->assertContains('other_page', $processedOptions['extras']['routes']);
    }
}
