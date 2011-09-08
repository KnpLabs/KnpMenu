<?php

namespace Knp\Menu\Renderer;

interface RendererProviderInterface
{
    /**
     * Retrieves a renderer by its name
     *
     * If null is given, a renderer marked as default is returned.
     *
     * @param string $name
     * @return \Knp\Menu\Renderer\RendererInterface
     * @throws \InvalidArgumentException if the renderer does not exists
     */
    function get($name = null);

    /**
     * Checks whether a renderer exists
     *
     * @param string $name
     * @return bool
     */
    function has($name);
}
