<?php

namespace Knp\Menu\Tests\Silex;

use Knp\Menu\Silex\RouterAwareFactory;

class RouterAwareFactoryTest extends \PHPUnit_Framework_TestCase
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
        $factory = new RouterAwareFactory($generator);
        $item = $factory->createItem('test_item', array('uri' => '/hello', 'route' => 'homepage'));
        $this->assertEquals('/foobar', $item->getUri());
    }

    public function testCreateItemWithRouteAndParameters()
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array('id' => 12), false)
            ->will($this->returnValue('/foobar'))
        ;
        $factory = new RouterAwareFactory($generator);
        $item = $factory->createItem('test_item', array('route' => 'homepage', 'routeParameters' => array('id' => 12)));
        $this->assertEquals('/foobar', $item->getUri());
    }

    public function testCreateItemWithAbsoluteRoute()
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array(), true)
            ->will($this->returnValue('http://php.net'))
        ;
        $factory = new RouterAwareFactory($generator);
        $item = $factory->createItem('test_item', array('route' => 'homepage', 'routeAbsolute' => true));
        $this->assertEquals('http://php.net', $item->getUri());
    }

    public function testCreateItemAppendsRouteUnderExtras()
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $factory = new RouterAwareFactory($generator);

        $item = $factory->createItem('test_item', array('route' => 'homepage'));
        $this->assertEquals(array('homepage'), $item->getExtra('routes'));

        $item = $factory->createItem('test_item', array('route' => 'homepage', 'extras' => array('routes' => array('other_page'))));
        $this->assertContains('homepage', $item->getExtra('routes'));
    }
}
