<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Voter based on the route
 */
class RequestStackRouteVoter implements VoterInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function matchItem(ItemInterface $item)
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return null;
        }

        $route = $request->attributes->get('_route');
        if (null === $route) {
            return null;
        }

        $routes = (array) $item->getExtra('routes', array());

        foreach ($routes as $testedRoute) {
            if (is_string($testedRoute)) {
                $testedRoute = array('route' => $testedRoute);
            }

            if (!is_array($testedRoute)) {
                throw new \InvalidArgumentException('Routes extra items must be strings or arrays.');
            }

            if ($this->isMatchingRoute($testedRoute)) {
                return true;
            }
        }

        return null;
    }

    private function isMatchingRoute(array $testedRoute)
    {
        $request = $this->requestStack->getCurrentRequest();

        $route = $request->attributes->get('_route');

        if (isset($testedRoute['route'])) {
            if ($route !== $testedRoute['route']) {
                return false;
            }
        } elseif (!empty($testedRoute['pattern'])) {
            if (!preg_match($testedRoute['pattern'], $route)) {
                return false;
            }
        } else {
            throw new \InvalidArgumentException('Routes extra items must have a "route" or "pattern" key.');
        }

        if (!isset($testedRoute['parameters'])) {
            return true;
        }

        $routeParameters = $request->attributes->get('_route_params', array());

        foreach ($testedRoute['parameters'] as $name => $value) {
            if (!isset($routeParameters[$name]) || $routeParameters[$name] !== (string) $value) {
                return false;
            }
        }

        return true;
    }
}
