<?php

namespace Knp\Menu\Renderer;

use Pimple\Container;

class PimpleProvider implements RendererProviderInterface
{
    private $app;
    private $rendererIds;
    private $defaultRenderer;

    public function __construct(Container $app, $defaultRenderer, array $rendererIds)
    {
        $this->app = $app;
        $this->rendererIds = $rendererIds;
        $this->defaultRenderer = $defaultRenderer;
    }

    public function get($name = null)
    {
        if (null === $name) {
            $name = $this->defaultRenderer;
        }

        if (!isset($this->rendererIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The renderer "%s" is not defined.', $name));
        }

        return $this->app[$this->rendererIds[$name]];
    }

    public function has($name)
    {
        return isset($this->rendererIds[$name]);
    }
}
