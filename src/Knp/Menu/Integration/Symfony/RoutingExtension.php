<?php

namespace Knp\Menu\Integration\Symfony;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Factory able to use the Symfony Routing component to build the url
 *
 * @final since 3.8.0
 */
class RoutingExtension implements ExtensionInterface
{
    public function __construct(private UrlGeneratorInterface $generator)
    {
    }

    public function buildOptions(array $options = []): array
    {
        if (!empty($options['route'])) {
            $params = $options['routeParameters'] ?? [];
            $absolute = (isset($options['routeAbsolute']) && $options['routeAbsolute']) ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH;
            $options['uri'] = $this->generator->generate($options['route'], $params, $absolute);

            // adding the item route to the extras under the 'routes' key (for the Silex RouteVoter)
            $options['extras']['routes'][] = [
                'route' => $options['route'],
                'parameters' => $params,
            ];
        }

        return $options;
    }

    public function buildItem(ItemInterface $item, array $options): void
    {
    }
}
