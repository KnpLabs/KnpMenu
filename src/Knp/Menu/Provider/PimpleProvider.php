<?php

namespace Knp\Menu\Provider;

class PimpleProvider implements MenuProviderInterface
{
    private $pimple;
    private $menuIds;

    public function __construct(\Pimple $pimple, array $menuIds = array())
    {
        $this->pimple = $pimple;
        $this->menuIds = $menuIds;
    }

    public function get($name, array $options = array())
    {
        if (!isset($this->menuIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        return $this->pimple[$this->menuIds[$name]];
    }

    public function has($name, array $options = array())
    {
        return isset($this->menuIds[$name]);
    }
}
