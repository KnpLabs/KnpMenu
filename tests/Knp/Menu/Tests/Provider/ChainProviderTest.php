<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\Provider\ChainProvider;

class ChainProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $innerProvider =  $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
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
        $provider = new ChainProvider(array($innerProvider));
        $this->assertTrue($provider->has('first'));
        $this->assertFalse($provider->has('second'));
    }

    public function testGetExistentMenu()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $innerProvider =  $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
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

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new ChainProvider(array());
        $provider->get('non-existent');
    }
}
