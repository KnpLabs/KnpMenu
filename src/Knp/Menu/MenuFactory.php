<?php

namespace Knp\Menu;

use Knp\Menu\Factory\CoreExtension;
use Knp\Menu\Factory\ExtensionInterface;

/**
 * Factory to create a menu from a tree
 */
class MenuFactory extends AbstractFactory
{
    protected function getMenuItem($name)
    {
        return new MenuItem($name, $this);
    }
}
