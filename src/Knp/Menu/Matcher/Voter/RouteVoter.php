<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Voter based on the route
 */
class RouteVoter implements VoterInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function matchItem(ItemInterface $item): ?bool
    {
        if (\method_exists($this->requestStack, 'getMainRequest')) {
            $request = $this->requestStack->getMainRequest();   // symfony 5.3+
        } else {
            $request = $this->requestStack->getMasterRequest();
        }

        if (null === $request) {
            return null;
        }

        $route = $request->attributes->get('_route');
        if (null === $route) {
            return null;
        }

        $routes = (array) $item->getExtra('routes', []);

        foreach ($routes as $testedRoute) {
            if (\is_string($testedRoute)) {
                $testedRoute = ['route' => $testedRoute];
            }

            if (!\is_array($testedRoute)) {
                throw new \InvalidArgumentException('Routes extra items must be strings or arrays.');
            }

            if ($this->isMatchingRoute($request, $testedRoute)) {
                return true;
            }
        }

        return null;
    }

    /**
     * @phpstan-param array{route?: string|null, pattern?: string|null, parameters?: array<string, mixed>, query_parameters?: array<string, string>} $testedRoute
     */
    private function isMatchingRoute(Request $request, array $testedRoute): bool
    {
        $route = $request->attributes->get('_route');

        if (isset($testedRoute['route'])) {
            if ($route !== $testedRoute['route']) {
                return false;
            }
        } elseif (!empty($testedRoute['pattern'])) {
            if (!\preg_match($testedRoute['pattern'], $route)) {
                return false;
            }
        } else {
            throw new \InvalidArgumentException('Routes extra items must have a "route" or "pattern" key.');
        }

        return $this->isMatchingParameters($request, $testedRoute) && $this->isMatchingQueryParameters($request, $testedRoute);
    }

    /**
     * @phpstan-param array{route?: string|null, pattern?: string|null, parameters?: array<string, mixed>, query_parameters?: array<string, string>} $testedRoute
     */
    private function isMatchingParameters(Request $request, array $testedRoute): bool
    {
        if (!isset($testedRoute['parameters'])) {
            return true;
        }

        $routeParameters = $request->attributes->get('_route_params', []);

        foreach ($testedRoute['parameters'] as $name => $value) {
            // cast both to string so that we handle integer and other non-string parameters, but don't stumble on 0 == 'abc'.
            if (!isset($routeParameters[$name]) || (string) $routeParameters[$name] !== (string) $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * @phpstan-param array{route?: string|null, pattern?: string|null, parameters?: array<string, mixed>, query_parameters?: array<string, string>} $testedRoute
     */
    private function isMatchingQueryParameters(Request $request, array $testedRoute): bool
    {
        if (!isset($testedRoute['query_parameters'])) {
            return true;
        }

        $routeQueryParameters = $request->query->all();

        foreach ($testedRoute['query_parameters'] as $name => $value) {
            // cast both to string so that we handle integer and other non-string parameters, but don't stumble on 0 == 'abc'.
            if (!isset($routeQueryParameters[$name]) || \is_array($routeQueryParameters[$name]) || (string) $routeQueryParameters[$name] !== (string) $value) {
                return false;
            }
        }

        return true;
    }
}
