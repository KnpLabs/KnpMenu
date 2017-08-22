<?php

namespace Knp\Menu\Integration\Symfony;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Factory able to use the Symfony2 Routing component to build the url
 */
class RoutingExtension implements ExtensionInterface
{
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function buildOptions(array $options = array())
    {
        if (!empty($options['route'])) {
            $params = isset($options['routeParameters']) ? $options['routeParameters'] : array();
            $absolute = (isset($options['routeAbsolute']) && $options['routeAbsolute']) ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH;
            $hash = (isset($options['hash']) && $options['hash'] && is_string($options['hash'])) ? $options['hash'] : null;
            $options['uri'] = $this->generator->generate($options['route'], $params, $absolute);
            if ($hash) { $options['uri'] .= "#".$hash; }

            // adding the item route to the extras under the 'routes' key (for the Silex RouteVoter)
            $options['extras']['routes'][] = array(
                'route' => $options['route'],
                'parameters' => $params,
                'hash' => $hash,
            );
        }

        return $options;
    }

    public function buildItem(ItemInterface $item, array $options)
    {
    }
}
