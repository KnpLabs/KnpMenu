<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\Matcher\Voter\RouteVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RouteVoterTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            $this->markTestSkipped('The Symfony HttpFoundation component is not available.');
        }
    }

    /**
     * @group legacy
     */
    public function testMatchingWithoutRequestAndStack()
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->never())
            ->method('getExtra');

        $voter = new RouteVoter();

        $this->assertNull($voter->matchItem($item));
    }

    public function testMatchingWithoutRequestInStack()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $this->markTestSkipped('The RequestStack is not available in this version of HttpFoundation.');
        }

        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->never())
            ->method('getExtra');

        $voter = new RouteVoter(new RequestStack());

        $this->assertNull($voter->matchItem($item));
    }

    /**
     * @group legacy
     */
    public function testMatchingWithoutStackRequestButLegacyRequest()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $this->markTestSkipped('The RequestStack is not available in this version of HttpFoundation.');
        }

        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->never())
            ->method('getExtra');

        $voter = new RouteVoter(new RequestStack());
        // the request set explicitly is ignored when a RequestStack is provided
        $voter->setRequest(new Request());

        $this->assertNull($voter->matchItem($item));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRouteConfig()
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->any())
            ->method('getExtra')
            ->with('routes')
            ->will($this->returnValue(array(array('invalid' => 'array'))));

        $request = new Request();
        $request->attributes->set('_route', 'foo');
        $request->attributes->set('_route_params', array());

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $voter = new RouteVoter($requestStack);

        $voter->matchItem($item);
    }

    /**
     * @param string       $route
     * @param array        $parameters
     * @param string|array $itemRoutes
     * @param boolean      $expected
     *
     * @dataProvider provideData
     */
    public function testMatching($route, array $parameters, $itemRoutes, $expected)
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->any())
            ->method('getExtra')
            ->with('routes')
            ->will($this->returnValue($itemRoutes))
        ;

        $request = new Request();
        $request->attributes->set('_route', $route);
        $request->attributes->set('_route_params', $parameters);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $voter = new RouteVoter($requestStack);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    /**
     * @group legacy
     */
    public function testMatchingWithRequest()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $this->markTestSkipped('The RequestStack is not available in this version of HttpFoundation.');
        }

        $item = $this->prophesize('Knp\Menu\ItemInterface');
        $item->getExtra('routes', array())->willReturn(array('foo'));

        $request = new Request();
        $request->attributes->set('_route', 'foo');
        $request->attributes->set('_route_params', array());

        $voter = new RouteVoter($request);

        $this->assertTrue($voter->matchItem($item->reveal()));
    }

    public function provideData()
    {
        return array(
            'no request route' => array(
                null,
                array(),
                'foo',
                null
            ),
            'integer parameters' => array(
                'foo',
                array('bar' => 128),
                array(array('route' => 'foo', 'parameters' => array('bar' => 128))),
                true
            ),
            'no item route' => array(
                'foo',
                array(),
                null,
                null
            ),
            'same single route' => array(
                'foo',
                array(),
                'foo',
                true
            ),
            'different single route' => array(
                'foo',
                array(),
                'bar',
                null
            ),
            'matching multiple routes' => array(
                'foo',
                array(),
                array('foo', 'baz'),
                true
            ),
            'matching multiple routes 2' => array(
                'baz',
                array(),
                array('foo', 'baz'),
                true
            ),
            'different multiple routes' => array(
                'foo',
                array(),
                array('bar', 'baz'),
                null
            ),
            'same single route with different parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('route' => 'foo', 'parameters' => array('1' => 'baz'))),
                null
            ),
            'same single route with same parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('route' => 'foo', 'parameters' => array('1' => 'bar'))),
                true
            ),
            'same single route with additional parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('route' => 'foo', 'parameters' => array('1' => 'bar', '2' => 'baz'))),
                null
            ),
            'same single route with less parameters' => array(
                'foo',
                array('1' => 'bar', '2' => 'baz'),
                array(array('route' => 'foo', 'parameters' => array('1' => 'bar'))),
                true
            ),
            'same single route with different type parameters' => array(
                'foo',
                array('1' => '2'),
                array(array('route' => 'foo', 'parameters' => array('1' => 2))),
                true
            ),
            'same route with multiple route params' => array(
                'foo',
                array('1' => 'bar'),
                array(
                    array('route' => 'foo', 'parameters' => array('1' => 'baz')),
                    array('route' => 'foo', 'parameters' => array('1' => 'bar')),
                ),
                true
            ),
            'same route with and without route params' => array(
                'foo',
                array('1' => 'bar'),
                array(
                    array('route' => 'foo', 'parameters' => array('1' => 'baz')),
                    array('route' => 'foo'),
                ),
                true
            ),
            'same route with multiple different route params' => array(
                'foo',
                array('1' => 'bar'),
                array(
                    array('route' => 'foo', 'parameters' => array('1' => 'baz')),
                    array('route' => 'foo', 'parameters' => array('1' => 'foo')),
                ),
                null
            ),
            'matching pattern without parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('pattern' => '/fo/')),
                true
            ),
            'non matching pattern without parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('pattern' => '/bar/')),
                null
            ),
            'matching pattern with parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('pattern' => '/fo/', 'parameters' => array('1' => 'bar'))),
                true
            ),
            'matching pattern with different parameters' => array(
                'foo', array('1' => 'bar'),
                array(array('pattern' => '/fo/', 'parameters' => array('1' => 'baz'))),
                null
            ),
        );
    }
}
