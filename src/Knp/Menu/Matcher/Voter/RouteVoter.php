<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Voter based on the route
 */
class RouteVoter implements VoterInterface
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function matchItem(ItemInterface $item)
    {
        if (null === $this->request) {
            return null;
        }

        $route = $this->request->attributes->get('_route');
        if (null === $route) {
            return null;
        }

        $routes = (array) $item->getExtra('routes', array());
        $parameters = (array) $item->getExtra('routesParameters', array());

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
        $route = $this->request->attributes->get('_route');

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

        $routeParameters = $this->request->attributes->get('_route_params', array());

        foreach ($testedRoute['parameters'] as $name => $value) {
            if (!isset($routeParameters[$name]) || $routeParameters[$name] !== (string) $value) {
                return false;
            }
        }

        return true;
    }
}
