<?php

namespace Knp\Menu\Provider;

use Knp\Menu\ItemInterface;
use Psr\Container\ContainerInterface;

/**
 * A menu provider getting the menus from a PSR-11 container.
 *
 * This menu provider does not support using options, as it cannot pass them to the container
 * to alter the menu building. Use a different provider in case you need support for options.
 *
 * @final since 3.8.0
 */
class PsrProvider implements MenuProviderInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        if (!$this->container->has($name)) {
            throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
        }

        return $this->container->get($name);
    }

    public function has(string $name, array $options = []): bool
    {
        return $this->container->has($name);
    }
}
