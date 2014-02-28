<?php

namespace Knp\Menu\Silex\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Menu\Matcher\Voter\RouteVoter as BaseRouteVoter;

/**
 * Voter based on the route
 */
class RouteVoter extends BaseRouteVoter
{
    public function __construct()
    {
        trigger_error(
            __CLASS__ . ' is deprecated because of namespace, use Knp\\Menu\\Matcher\\RouteVoter instead.',
            E_USER_DEPRECATED
        );
    }
}
