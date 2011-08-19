<?php

namespace Knp\Menu\Renderer;

class PimpleProvider implements RendererProviderInterface
{
    private $pimple;
    private $rendererIds;

    public function __construct(\Pimple $pimple, array $rendererIds = array())
    {
        $this->pimple = $pimple;
        $this->rendererIds = $rendererIds;
    }

    public function get($name)
    {
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
