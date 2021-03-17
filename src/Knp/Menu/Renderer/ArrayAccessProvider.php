<?php

namespace Knp\Menu\Renderer;

/**
 * A renderer provider getting the renderers from a class implementing ArrayAccess.
 */
class ArrayAccessProvider implements RendererProviderInterface
{
    /**
     * @var \ArrayAccess
     */
    private $registry;

    /**
     * @var array
     */
    private $rendererIds;

    /**
     * @var string
     */
    private $defaultRenderer;

    /**
     * @param string $defaultRenderer The name of the renderer used by default
     * @param array  $rendererIds     The map between renderer names and registry keys
     */
    public function __construct(\ArrayAccess $registry, string $defaultRenderer, array $rendererIds)
    {
        $this->registry = $registry;
        $this->rendererIds = $rendererIds;
        $this->defaultRenderer = $defaultRenderer;
    }

    public function get(?string $name = null): RendererInterface
    {
        if (null === $name) {
            $name = $this->defaultRenderer;
        }

        if (!isset($this->rendererIds[$name])) {
            throw new \InvalidArgumentException(\sprintf('The renderer "%s" is not defined.', $name));
        }

        return $this->registry[$this->rendererIds[$name]];
    }

    public function has($name): bool
    {
        return isset($this->rendererIds[$name]);
    }
}
