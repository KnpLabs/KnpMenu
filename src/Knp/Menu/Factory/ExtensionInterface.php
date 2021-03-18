<?php

namespace Knp\Menu\Factory;

use Knp\Menu\ItemInterface;

interface ExtensionInterface
{
    /**
     * Builds the full option array used to configure the item.
     *
     * @param array<string, mixed> $options The options processed by the previous extensions
     *
     * @return array<string, mixed>
     */
    public function buildOptions(array $options): array;

    /**
     * Configures the item with the passed options
     *
     * @param array<string, mixed> $options
     */
    public function buildItem(ItemInterface $item, array $options): void;
}
