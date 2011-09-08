<?php

namespace Knp\Menu\Renderer;

class PimpleProvider implements RendererProviderInterface
{
    private $pimple;
    private $rendererIds;
    private $defaultRenderer;

    public function __construct(\Pimple $pimple, $defaultRenderer, array $rendererIds)
    {
        $this->pimple = $pimple;
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

        return $this->pimple[$this->rendererIds[$name]];
    }

    public function has($name)
    {
        return isset($this->rendererIds[$name]);
    }
}
