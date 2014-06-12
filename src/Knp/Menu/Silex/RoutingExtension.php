<?php

namespace Knp\Menu\Silex;

use Knp\Menu\Integration\Symfony\RoutingExtension as BaseRoutingExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RoutingExtension extends BaseRoutingExtension
{
    public function __construct(UrlGeneratorInterface $generator)
    {
        trigger_error(
            __CLASS__ . ' is deprecated, use Knp\Menu\Integration\Symfony\RoutingExtension instead.',
            E_USER_DEPRECATED
        );
        
        parent::__construct($generator);
    }
}
