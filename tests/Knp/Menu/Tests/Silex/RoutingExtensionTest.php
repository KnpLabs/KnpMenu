<?php

namespace Knp\Menu\Tests\Silex;

use Knp\Menu\Silex\RoutingExtension;

class RoutingExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!interface_exists('Symfony\Component\Routing\Generator\UrlGeneratorInterface')) {
            $this->markTestSkipped('The Symfony2 Routing component is not available');
        }
    }

    public function testCreateItemWithRoute()
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array(), false)
            ->will($this->returnValue('/foobar'))
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(array('uri' => '/hello', 'route' => 'homepage', 'label' => 'foo'));

        $this->assertEquals('/foobar', $processedOptions['uri']);
        $this->assertEquals('foo', $processedOptions['label']);
    }

    public function testCreateItemWithRouteAndParameters()
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array('id' => 12), false)
            ->will($this->returnValue('/foobar'))
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(array('route' => 'homepage', 'routeParameters' => array('id' => 12)));

        $this->assertEquals('/foobar', $processedOptions['uri']);
    }

    public function testCreateItemWithAbsoluteRoute()
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array(), true)
            ->will($this->returnValue('http://php.net'))
        ;

        $extension = new RoutingExtension($generator);

        $processedOptions = $extension->buildOptions(array('route' => 'homepage', 'routeAbsolute' => true));

        $this->assertEquals('http://php.net', $processedOptions['uri']);
    }

    public function testCreateItemAppendsRouteUnderExtras()
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');

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
