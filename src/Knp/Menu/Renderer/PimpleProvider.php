<?php

namespace Knp\Menu\Renderer;

/**
 * Class PimpleProvider
 *
 * @package Knp\Menu\Renderer
 */
class PimpleProvider implements RendererProviderInterface
{
    /**
     * @var \Pimple
     */
    private $pimple;

    /**
     * @var array
     */
    private $rendererIds;

    /**
     * @var
     */
    private $defaultRenderer; // TODO which type is this var ?

    /**
     * @param \Pimple $pimple
     * @param         $defaultRenderer
     * @param array   $rendererIds
     */
    public function __construct(\Pimple $pimple, $defaultRenderer, array $rendererIds)
    {
        $this->pimple          = $pimple;
        $this->rendererIds     = $rendererIds;
        $this->defaultRenderer = $defaultRenderer;
    }

    /**
     * @param null $name
     *
     * @return mixed
     */
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

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->rendererIds[$name]);
    }
}
