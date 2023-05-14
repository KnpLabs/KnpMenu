<?php

namespace Knp\Menu\Renderer;

/**
 * A renderer provider getting the renderers from a class implementing ArrayAccess.
 */
class ArrayAccessProvider implements RendererProviderInterface
{
    /**
     * @param \ArrayAccess<string, RendererInterface> $registry
     * @param string                                  $defaultRenderer The name of the renderer used by default
     * @param array<string, string>                   $rendererIds     The map between renderer names and registry keys
     */
    public function __construct(private \ArrayAccess $registry, private string $defaultRenderer, private array $rendererIds)
    {
    }

    public function get(?string $name = null): RendererInterface
    {
        if (null === $name) {
            $name = $this->defaultRenderer;
        }

        if (!isset($this->rendererIds[$name]) || null === $this->registry[$this->rendererIds[$name]]) {
            throw new \InvalidArgumentException(\sprintf('The renderer "%s" is not defined.', $name));
        }

        return $this->registry[$this->rendererIds[$name]];
    }

    public function has(string $name): bool
    {
        return isset($this->rendererIds[$name]);
    }
}
