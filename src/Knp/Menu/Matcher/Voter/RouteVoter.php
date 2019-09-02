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
    /**
     * @var RequestStack|null
     */
    private $requestStack;

    /**
     * @var Request|null
     */
    private $request;

    public function __construct($requestStack = null)
    {
        if ($requestStack instanceof RequestStack) {
            $this->requestStack = $requestStack;
        } elseif ($requestStack instanceof Request) {
            @trigger_error(sprintf('Passing a Request as the first argument for "%s" constructor is deprecated since version 2.3 and won\'t be possible in 3.0. Pass a RequestStack instead.', __CLASS__), E_USER_DEPRECATED);

            // BC layer for the old API of the class
            $this->request = $requestStack;
        } elseif (null !== $requestStack) {
            throw new \InvalidArgumentException('The first argument of %s must be null, a RequestStack or a Request. %s given', __CLASS__, is_object($requestStack) ? get_class($requestStack) :  gettype($requestStack));
        } else {
            @trigger_error(sprintf('Not passing a RequestStack as the first argument for "%s" constructor is deprecated since version 2.3 and won\'t be possible in 3.0.', __CLASS__), E_USER_DEPRECATED);
        }
    }

    /**
     * Sets the request against which the menu should be matched.
     *
     * This Request is ignored in case a RequestStack is passed in the constructor.
     *
     * @deprecated since version 2.3. Pass a RequestStack to the constructor instead.
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        @trigger_error(\sprintf('The %s() method is deprecated since version 2.3 and will be removed in 3.0. Pass a RequestStack in the constructor instead.', __METHOD__), E_USER_DEPRECATED);

        $this->request = $request;
    }

    public function matchItem(ItemInterface $item)
    {
        if (null !== $this->requestStack) {
            $request = $this->requestStack->getMasterRequest();
        } else {
            $request = $this->request;
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

    private function isMatchingRoute(Request $request, array $testedRoute)
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
}
