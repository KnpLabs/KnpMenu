<?php

namespace Knp\Menu\Loader;

use Knp\Menu\ItemInterface;

interface LoaderInterface
{
    /**
     * Loads the data into a menu item
     *
     * @param mixed $data
     *
     * @return ItemInterface
     */
    public function load($data): ItemInterface;

    /**
     * Checks whether the loader can load these data
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data): bool;
}
