<?php

namespace Knp\Menu\Silex\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
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
            if ($route !== $testedRoute) {
                continue;
            }

            if (isset($parameters[$route])) {
                foreach ($parameters[$route] as $name => $value) {
                    if ($this->request->attributes->get($name) != $value) {
                        return null;
                    }
                }
            }

            return true;
        }

        return null;
    }
}
