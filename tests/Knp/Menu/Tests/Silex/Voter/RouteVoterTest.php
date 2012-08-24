<?php

namespace Knp\Menu\Tests\Silex\Voter;

use Knp\Menu\Silex\Voter\RouteVoter;
use Symfony\Component\HttpFoundation\Request;

class RouteVoterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            $this->markTestSkipped('The Symfony HttpFoundation component is not available.');
        }
    }

    public function testMatchingWithoutRequest()
    {
        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->never())
            ->method('getExtra');

        $voter = new RouteVoter();

        $this->assertNull($voter->matchItem($item));
    }

    /**
     * @param string       $route
     * @param string|array $itemRoutes
     * @param boolean      $expected
     *
     * @dataProvider provideData
     */
    public function testMatching($route, $itemRoutes, $expected)
    {
        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->any())
            ->method('getExtra')
            ->with($this->equalTo('routes'))
            ->will($this->returnValue($itemRoutes));

        $request = new Request();
        $request->attributes->set('_route', $route);

        $voter = new RouteVoter();
        $voter->setRequest($request);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    public function provideData()
    {
        return array(
            'no request route' => array(null, 'foo', null),
            'no item route' => array('foo', null, null),
            'same single route' => array('foo', 'foo', true),
            'different single route' => array('foo', 'bar', null),
            'matching mutiple routes' => array('foo', array('foo', 'baz'), true),
            'different single route' => array('foo', array('bar', 'baz'), null),
        );
    }
}
