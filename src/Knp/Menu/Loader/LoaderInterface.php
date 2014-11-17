<?php

namespace Knp\Menu\Loader;

use Knp\Menu\ItemInterface;

/**
 * Interface LoaderInterface
 *
 * @package Knp\Menu\Loader
 */
interface LoaderInterface
{
    /**
     * Loads the data into a menu item
     *
     * @param mixed $data
     *
     * @return ItemInterface
     */
    public function load($data);

    /**
     * Checks whether the loader can load these data
     *
     * @param mixed $data
     *
     * @return boolean
     */
    public function supports($data);
}
