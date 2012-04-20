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
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Checks whether an item is current.
     *
     * If the voter is not able to determine a result,
     * it should return null to let other voters do the job.
     *
     * @param \Knp\Menu\ItemInterface $item
     * @return boolean|null
     */
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
        if (in_array($route, $routes)) {
            return true;
        }

        return null;
    }
}
