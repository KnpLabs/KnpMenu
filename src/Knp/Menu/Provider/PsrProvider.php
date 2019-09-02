<?php

namespace Knp\Menu\Provider;

use Psr\Container\ContainerInterface;

/**
 * A menu provider getting the menus from a PSR-11 container.
 *
 * This menu provider does not support using options, as it cannot pass them to the container
 * to alter the menu building. Use a different provider in case you need support for options.
 */
class PsrProvider implements MenuProviderInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get($name, array $options = [])
    {
        if (!$this->container->has($name)) {
            throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
        }

        return $this->container->get($name);
    }

    public function has($name, array $options = [])
    {
        return $this->container->has($name);
    }
}
