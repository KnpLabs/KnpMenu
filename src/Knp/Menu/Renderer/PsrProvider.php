<?php

namespace Knp\Menu\Renderer;

use Psr\Container\ContainerInterface;

/**
 * A renderer provider getting the renderer from a PSR-11 container.
 *
 * This menu provider does not support using options, as it cannot pass them to the container
 * to alter the menu building. Use a different provider in case you need support for options.
 */
class PsrProvider implements RendererProviderInterface
{
    private $container;
    private $defaultRenderer;

    /**
     * PsrProvider constructor.
     *
     * @param ContainerInterface $container
     * @param string             $defaultRenderer id of the default renderer (it should exist in the container to avoid weird failures)
     */
    public function __construct(ContainerInterface $container, $defaultRenderer)
    {
        $this->container = $container;
        $this->defaultRenderer = $defaultRenderer;
    }

    public function get(?string $name = null): RendererInterface
    {
        if (null === $name) {
            $name = $this->defaultRenderer;
        }

        if (!$this->container->has($name)) {
            throw new \InvalidArgumentException(\sprintf('The renderer "%s" is not defined.', $name));
        }

        return $this->container->get($name);
    }

    public function has(string $name): bool
    {
        return $this->container->has($name);
    }
}
