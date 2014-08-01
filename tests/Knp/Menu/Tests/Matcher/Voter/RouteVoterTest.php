<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\Matcher\Voter\RouteVoter;
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
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRouteConfig()
    {
        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->any())
            ->method('getExtra')
            ->with($this->logicalOr($this->equalTo('routes'), $this->equalTo('routesParameters')))
            ->will($this->returnCallback(function ($parameter) {
                switch ($parameter) {
                    case 'routes':
                        return array(array('invalid' => 'array'));
                    case 'routesParameters':
                        return array();
                }
            }));

        $request = new Request();
        $request->attributes->set('_route', 'foo');
        $request->attributes->set('_route_params', array());

        $voter = new RouteVoter($request);

        $voter->matchItem($item);
    }

    /**
     * @param string       $route
     * @param array        $parameters
     * @param string|array $itemRoutes
     * @param array        $itemsRoutesParameters
     * @param boolean      $expected
     *
     * @dataProvider provideData
     */
    public function testMatching($route, array $parameters, $itemRoutes, array $itemsRoutesParameters, $expected)
    {
        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->any())
            ->method('getExtra')
            ->with($this->logicalOr($this->equalTo('routes'), $this->equalTo('routesParameters')))
            ->will($this->returnCallback(function ($parameter) use ($itemRoutes, $itemsRoutesParameters) {
                switch ($parameter) {
                    case 'routes':
                        return $itemRoutes;
                    case 'routesParameters':
                        return $itemsRoutesParameters;
                }
            }))
        ;

        $request = new Request();
        $request->attributes->set('_route', $route);
        $request->attributes->set('_route_params', $parameters);

        $voter = new RouteVoter($request);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    public function provideData()
    {
        return array(
            'no request route' => array(
                null,
                array(),
                'foo',
                array(),
                null
            ),
            'integer parameters' => array(
                'foo',
                array('bar' => 128),
                array(array('route' => 'foo', 'parameters' => array('bar' => 128))),
                array(),
                null
            ),
            'no item route' => array(
                'foo',
                array(),
                null,
                array(),
                null
            ),
            'same single route' => array(
                'foo',
                array(),
                'foo',
                array(),
                true
            ),
            'different single route' => array(
                'foo',
                array(),
                'bar',
                array(),
                null
            ),
            'matching multiple routes' => array(
                'foo',
                array(),
                array('foo', 'baz'),
                array(),
                true
            ),
            'matching multiple routes 2' => array(
                'baz',
                array(),
                array('foo', 'baz'),
                array(),
                true
            ),
            'different multiple routes' => array(
                'foo',
                array(),
                array('bar', 'baz'),
                array(),
                null
            ),
            'same single route with different parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('route' => 'foo', 'parameters' => array('1' => 'baz'))),
                array(),
                null
            ),
            'same single route with same parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('route' => 'foo', 'parameters' => array('1' => 'bar'))),
                array(),
                true
            ),
            'same single route with additional parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('route' => 'foo', 'parameters' => array('1' => 'bar', '2' >+ 'baz'))),
                array(),
                null
            ),
            'same single route with less parameters' => array(
                'foo',
                array('1' => 'bar', '2' => 'baz'),
                array(array('route' => 'foo', 'parameters' => array('1' => 'bar'))),
                array(),
                true
            ),
            'same single route with different type parameters' => array(
                'foo',
                array('1' => '2'),
                array(array('route' => 'foo', 'parameters' => array('1' => 2))),
                array(),
                true
            ),
            'same route with multiple route params' => array(
                'foo',
                array('1' => 'bar'),
                array(
                    array('route' => 'foo', 'parameters' => array('1' => 'baz')),
                    array('route' => 'foo', 'parameters' => array('1' => 'bar')),
                ),
                array(),
                true
            ),
            'same route with and without route params' => array(
                'foo',
                array('1' => 'bar'),
                array(
                    array('route' => 'foo', 'parameters' => array('1' => 'baz')),
                    array('route' => 'foo'),
                ),
                array(),
                true
            ),
            'same route with multiple different route params' => array(
                'foo',
                array('1' => 'bar'),
                array(
                    array('route' => 'foo', 'parameters' => array('1' => 'baz')),
                    array('route' => 'foo', 'parameters' => array('1' => 'foo')),
                ),
                array(),
                null
            ),
            'matching pattern without parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('pattern' => '/fo/')),
                array(),
                true
            ),
            'non matching pattern without parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('pattern' => '/bar/')),
                array(),
                null
            ),
            'matching pattern with parameters' => array(
                'foo',
                array('1' => 'bar'),
                array(array('pattern' => '/fo/', 'parameters' => array('1' => 'bar'))),
                array(),
                true
            ),
            'matching pattern with different parameters' => array(
                'foo', array('1' => 'bar'),
                array(array('pattern' => '/fo/', 'parameters' => array('1' => 'baz'))),
                array(),
                null
            ),
        );
    }
}
