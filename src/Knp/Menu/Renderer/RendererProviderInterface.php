<?php

namespace Knp\Menu\Renderer;

interface RendererProviderInterface
{
    /**
     * Retrieves a renderer by its name
     *
     * If null is given, a renderer marked as default is returned.
     *
     * @param string|null $name
     *
     * @return RendererInterface
     *
     * @throws \InvalidArgumentException if the renderer does not exists
     */
    public function get(?string $name = null): RendererInterface;

    /**
     * Checks whether a renderer exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;
}
