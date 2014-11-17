<?php

namespace Knp\Menu\Provider;

/**
 * Class PimpleProvider
 *
 * @package Knp\Menu\Provider
 */
class PimpleProvider implements MenuProviderInterface
{
    /**
     * @var \Pimple
     */
    private $pimple;

    /**
     * @var array
     */
    private $menuIds;

    /**
     * @param \Pimple $pimple
     * @param array   $menuIds
     */
    public function __construct(\Pimple $pimple, array $menuIds = array())
    {
        $this->pimple  = $pimple;
        $this->menuIds = $menuIds;
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return mixed
     */
    public function get($name, array $options = array())
    {
        if (!isset($this->menuIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        $menu = $this->pimple[$this->menuIds[$name]];

        if ($menu instanceof \Closure) {
            $menu = $menu($options, $this->pimple);
        }

        return $menu;
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return bool
     */
    public function has($name, array $options = array())
    {
        return isset($this->menuIds[$name]);
    }
}
