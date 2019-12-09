<?php

namespace Knp\Menu\Provider;

use Knp\Menu\ItemInterface;

/**
 * A menu provider getting the menus from a class implementing ArrayAccess.
 *
 * In case the value stored in the registry is a callable rather than an ItemInterface,
 * it will be called with the options as first argument and the registry as second argument
 * and is expected to return a menu item.
 */
class ArrayAccessProvider implements MenuProviderInterface
{
    private $registry;
    private $menuIds;

    /**
     * @param \ArrayAccess $registry
     * @param array        $menuIds  The map between menu identifiers and registry keys
     */
    public function __construct(\ArrayAccess $registry, array $menuIds = [])
    {
        $this->registry = $registry;
        $this->menuIds = $menuIds;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        if (!isset($this->menuIds[$name])) {
            throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
        }

        $menu = $this->registry[$this->menuIds[$name]];

        if (\is_callable($menu)) {
            $menu = \call_user_func($menu, $options, $this->registry);
        }

        return $menu;
    }

    public function has(string $name, array $options = []): bool
    {
        return isset($this->menuIds[$name]);
    }
}
