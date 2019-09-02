<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\Matcher\Voter\RouteVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RouteVoterTest extends TestCase
{
    protected function setUp()
    {
        if (!\class_exists('Symfony\Component\HttpFoundation\Request')) {
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
            ->will($this->returnValue([['invalid' => 'array']]));

        $request = new Request();
        $request->attributes->set('_route', 'foo');
        $request->attributes->set('_route_params', []);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $voter = new RouteVoter($requestStack);

        $voter->matchItem($item);
    }

    /**
     * @param string       $route
     * @param array        $parameters
     * @param string|array $itemRoutes
     * @param bool         $expected
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
        $item->getExtra('routes', [])->willReturn(['foo']);

        $request = new Request();
        $request->attributes->set('_route', 'foo');
        $request->attributes->set('_route_params', []);

        $voter = new RouteVoter($request);

        $this->assertTrue($voter->matchItem($item->reveal()));
    }

    public function provideData()
    {
        return [
            'no request route' => [
                null,
                [],
                'foo',
                null,
            ],
            'integer parameters' => [
                'foo',
                ['bar' => 128],
                [['route' => 'foo', 'parameters' => ['bar' => 128]]],
                true,
            ],
            'no item route' => [
                'foo',
                [],
                null,
                null,
            ],
            'same single route' => [
                'foo',
                [],
                'foo',
                true,
            ],
            'different single route' => [
                'foo',
                [],
                'bar',
                null,
            ],
            'matching multiple routes' => [
                'foo',
                [],
                ['foo', 'baz'],
                true,
            ],
            'matching multiple routes 2' => [
                'baz',
                [],
                ['foo', 'baz'],
                true,
            ],
            'different multiple routes' => [
                'foo',
                [],
                ['bar', 'baz'],
                null,
            ],
            'same single route with different parameters' => [
                'foo',
                ['1' => 'bar'],
                [['route' => 'foo', 'parameters' => ['1' => 'baz']]],
                null,
            ],
            'same single route with same parameters' => [
                'foo',
                ['1' => 'bar'],
                [['route' => 'foo', 'parameters' => ['1' => 'bar']]],
                true,
            ],
            'same single route with additional parameters' => [
                'foo',
                ['1' => 'bar'],
                [['route' => 'foo', 'parameters' => ['1' => 'bar', '2' => 'baz']]],
                null,
            ],
            'same single route with less parameters' => [
                'foo',
                ['1' => 'bar', '2' => 'baz'],
                [['route' => 'foo', 'parameters' => ['1' => 'bar']]],
                true,
            ],
            'same single route with different type parameters' => [
                'foo',
                ['1' => '2'],
                [['route' => 'foo', 'parameters' => ['1' => 2]]],
                true,
            ],
            'same route with multiple route params' => [
                'foo',
                ['1' => 'bar'],
                [
                    ['route' => 'foo', 'parameters' => ['1' => 'baz']],
                    ['route' => 'foo', 'parameters' => ['1' => 'bar']],
                ],
                true,
            ],
            'same route with and without route params' => [
                'foo',
                ['1' => 'bar'],
                [
                    ['route' => 'foo', 'parameters' => ['1' => 'baz']],
                    ['route' => 'foo'],
                ],
                true,
            ],
            'same route with multiple different route params' => [
                'foo',
                ['1' => 'bar'],
                [
                    ['route' => 'foo', 'parameters' => ['1' => 'baz']],
                    ['route' => 'foo', 'parameters' => ['1' => 'foo']],
                ],
                null,
            ],
            'matching pattern without parameters' => [
                'foo',
                ['1' => 'bar'],
                [['pattern' => '/fo/']],
                true,
            ],
            'non matching pattern without parameters' => [
                'foo',
                ['1' => 'bar'],
                [['pattern' => '/bar/']],
                null,
            ],
            'matching pattern with parameters' => [
                'foo',
                ['1' => 'bar'],
                [['pattern' => '/fo/', 'parameters' => ['1' => 'bar']]],
                true,
            ],
            'matching pattern with different parameters' => [
                'foo', ['1' => 'bar'],
                [['pattern' => '/fo/', 'parameters' => ['1' => 'baz']]],
                null,
            ],
        ];
    }
}
