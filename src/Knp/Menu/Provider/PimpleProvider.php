<?php

namespace Knp\Menu\Provider;

use Pimple\Container;

class PimpleProvider implements MenuProviderInterface
{
    private $app;
    private $menuIds;

    public function __construct(Container $app, array $menuIds = array())
    {
        $this->app = $app;
        $this->menuIds = $menuIds;
    }

    public function get($name, array $options = array())
    {
        if (!isset($this->menuIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        $menu = $this->app[$this->menuIds[$name]];

        if ($menu instanceof \Closure) {
            $menu = $menu($options, $this->app);
        }

        return $menu;
    }

    public function has($name, array $options = array())
    {
        return isset($this->menuIds[$name]);
    }
}
