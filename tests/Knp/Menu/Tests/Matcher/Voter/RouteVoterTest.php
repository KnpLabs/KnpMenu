<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\RouteVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RouteVoterTest extends TestCase
{
    public function testMatchingWithoutRequestInStack(): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->never())
            ->method('getExtra');

        $voter = new RouteVoter(new RequestStack());

        $this->assertNull($voter->matchItem($item));
    }

    public function testInvalidRouteConfig(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->any())
            ->method('getExtra')
            ->with('routes')
            ->willReturn([['invalid' => 'array']]);

        $request = new Request();
        $request->attributes->set('_route', 'foo');
        $request->attributes->set('_route_params', []);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $voter = new RouteVoter($requestStack);

        $voter->matchItem($item);
    }

    /**
     * @param string|array<string, mixed> $itemRoutes
     * @param array<string, mixed>        $parameters
     *
     * @dataProvider provideData
     */
    public function testMatching(?string $route, array $parameters, $itemRoutes, ?bool $expected): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->any())
            ->method('getExtra')
            ->with('routes')
            ->willReturn($itemRoutes)
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
     * @return array<string, array<int, mixed>>
     */
    public function provideData(): array
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
                'foo',
                ['1' => 'bar'],
                [['pattern' => '/fo/', 'parameters' => ['1' => 'baz']]],
                null,
            ],
        ];
    }
}
