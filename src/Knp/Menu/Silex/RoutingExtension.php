<?php

namespace Knp\Menu\Silex;

class RoutingExtension
{
    public function __construct()
    {
        trigger_error(
            __CLASS__ . ' is deprecated because of namespace, use Knp\Menu\Integration\Symfony\RoutingExtension instead.',
            E_USER_DEPRECATED
        );
    }
}
