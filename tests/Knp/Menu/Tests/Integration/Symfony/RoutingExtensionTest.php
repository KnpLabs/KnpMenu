<?php

namespace Knp\Menu\Tests\Silex;

use Knp\Menu\Integration\Symfony\RoutingExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RoutingExtensionTest extends TestCase
{
    protected function setUp()
    {
        if (!interface_exists('Symfony\Component\Routing\Generator\UrlGeneratorInterface')) {
            $this->markTestSkipped('The Symfony Routing component is not available');
        }
    }

    public function testCreateItemWithRoute()
    {
        $generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array(), UrlGeneratorInterface::ABSOLUTE_PATH)
            ->will($this->returnValue('/foobar'))
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(array('uri' => '/hello', 'route' => 'homepage', 'label' => 'foo'));

        $this->assertEquals('/foobar', $processedOptions['uri']);
        $this->assertEquals('foo', $processedOptions['label']);
    }

    public function testCreateItemWithRouteAndParameters()
    {
        $generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array('id' => 12), UrlGeneratorInterface::ABSOLUTE_PATH)
            ->will($this->returnValue('/foobar'))
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(array('route' => 'homepage', 'routeParameters' => array('id' => 12)));

        $this->assertEquals('/foobar', $processedOptions['uri']);
    }

    public function testCreateItemWithAbsoluteRoute()
    {
        $generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL)
            ->will($this->returnValue('http://php.net'))
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(array('route' => 'homepage', 'routeAbsolute' => true));

        $this->assertEquals('http://php.net', $processedOptions['uri']);
    }

    public function testCreateItemAppendsRouteUnderExtras()
    {
        $generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->getMock();

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions( array('route' => 'homepage'));
        $this->assertEquals(array(array('route' => 'homepage', 'parameters' => array())), $processedOptions['extras']['routes']);

        $processedOptions = $extension->buildOptions( array('route' => 'homepage', 'routeParameters' => array('bar' => 'baz')));
        $this->assertEquals(array(array('route' => 'homepage', 'parameters' => array('bar' => 'baz'))), $processedOptions['extras']['routes']);

        $processedOptions = $extension->buildOptions( array('route' => 'homepage', 'extras' => array('routes' => array('other_page'))));
        $this->assertContains(array('route' => 'homepage', 'parameters' => array()), $processedOptions['extras']['routes']);
        $this->assertContains('other_page', $processedOptions['extras']['routes']);
    }
}
