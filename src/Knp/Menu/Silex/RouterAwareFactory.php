<?php

namespace Knp\Menu\Silex;

use Knp\Menu\MenuFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Factory able to use the Symfony2 Routing component to build the url
 */
class RouterAwareFactory extends MenuFactory
{
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    protected function buildOptions(array $options = array())
    {
        if (!empty($options['route'])) {
            $params = isset($options['routeParameters']) ? $options['routeParameters'] : array();
            $absolute = isset($options['routeAbsolute']) ? $options['routeAbsolute'] : false;
            $options['uri'] = $this->generator->generate($options['route'], $params, $absolute);

            // adding the item route to the extras under the 'routes' key (for the Silex RouteVoter)
            $options = array_merge_recursive(array('extras' => array(
                'routes' => array($options['route']),
                'routesParameters' => array($options['route']=>$params),
            )), $options);
        }

        return parent::buildOptions($options);
    }
}
