<?php

namespace Knp\Menu\Factory;

use Knp\Menu\ItemInterface;

/**
 * Interface ExtensionInterface
 *
 * @package Knp\Menu\Factory
 */
interface ExtensionInterface
{
    /**
     * Builds the full option array used to configure the item.
     *
     * @param array $options The options processed by the previous extensions
     *
     * @return array
     */
    public function buildOptions(array $options);

    /**
     * Configures the item with the passed options
     *
     * @param ItemInterface $item
     * @param array         $options
     */
    public function buildItem(ItemInterface $item, array $options);
}
