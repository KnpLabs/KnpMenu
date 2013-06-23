<?php

namespace Knp\Menu\Silex;

use Knp\Menu\MenuFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Factory able to use the Symfony2 Routing component to build the url
 *
 * @deprecated Use Knp\Menu\Silex\RoutingExtension instead
 */
class RouterAwareFactory extends MenuFactory
{
    public function __construct(UrlGeneratorInterface $generator)
    {
        trigger_error(__CLASS__ . ' is deprecated. Use Knp\Menu\Silex\RoutingExtension instead.', E_USER_DEPRECATED);

        parent::__construct();
        $this->addExtension(new RoutingExtension($generator));
    }
}
