<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\Provider\ChainProvider;
use PHPUnit\Framework\TestCase;

class ChainProviderTest extends TestCase
{
    public function testHas()
    {
        $innerProvider =  $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $innerProvider->expects($this->at(0))
            ->method('has')
            ->with('first')
            ->will($this->returnValue(true))
        ;
        $innerProvider->expects($this->at(1))
            ->method('has')
            ->with('second')
            ->will($this->returnValue(false))
        ;
        $innerProvider->expects($this->at(2))
            ->method('has')
            ->with('third', array('foo' => 'bar'))
            ->will($this->returnValue(false))
        ;
        $provider = new ChainProvider(array($innerProvider));
        $this->assertTrue($provider->has('first'));
        $this->assertFalse($provider->has('second'));
        $this->assertFalse($provider->has('third', array('foo' => 'bar')));
    }

    public function testGetExistentMenu()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $innerProvider =  $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $innerProvider->expects($this->any())
            ->method('has')
            ->with('default')
            ->will($this->returnValue(true))
        ;
        $innerProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $provider = new ChainProvider(array($innerProvider));
        $this->assertSame($menu, $provider->get('default'));
    }

    public function testGetWithOptions()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $innerProvider =  $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $innerProvider->expects($this->any())
            ->method('has')
            ->with('default', array('foo' => 'bar'))
            ->will($this->returnValue(true))
        ;
        $innerProvider->expects($this->once())
            ->method('get')
            ->with('default', array('foo' => 'bar'))
            ->will($this->returnValue($menu))
        ;

        $provider = new ChainProvider(array($innerProvider));
        $this->assertSame($menu, $provider->get('default', array('foo' => 'bar')));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new ChainProvider(array());
        $provider->get('non-existent');
    }

    public function testIterator()
    {
        $menu = $this->prophesize('Knp\Menu\ItemInterface');

        $innerProvider = $this->prophesize('Knp\Menu\Provider\MenuProviderInterface');
        $innerProvider->has('foo', array())->willReturn(true);
        $innerProvider->get('foo', array())->willReturn($menu);

        $provider = new ChainProvider(new \ArrayIterator(array($innerProvider->reveal())));
        $this->assertTrue($provider->has('foo'));
        $this->assertSame($menu->reveal(), $provider->get('foo'));
    }
}
