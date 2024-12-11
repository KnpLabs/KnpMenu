<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Util\MenuManipulator;
use Twig\Extension\RuntimeExtensionInterface;

class MenuRuntimeExtension implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly Helper $helper,
        private readonly ?MatcherInterface $matcher = null,
        private readonly ?MenuManipulator $menuManipulator = null,
    ) {
    }

    /**
     * Retrieves an item following a path in the tree.
     *
     * @param array<int, string>   $path
     * @param array<string, mixed> $options
     */
    public function get(ItemInterface|string $menu, array $path = [], array $options = []): ItemInterface
    {
        return $this->helper->get($menu, $path, $options);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param string|ItemInterface|array<ItemInterface|string> $menu
     * @param array<string, mixed>                             $options
     */
    public function render(array|ItemInterface|string $menu, array $options = [], ?string $renderer = null): string
    {
        return $this->helper->render($menu, $options, $renderer);
    }

    /**
     * Returns an array ready to be used for breadcrumbs.
     *
     * @param string|ItemInterface|array<ItemInterface|string> $menu
     * @param string|array<string|null>|null                   $subItem
     *
     * @phpstan-param string|ItemInterface|array<int|string, string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface>|\Traversable<string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface> $subItem
     *
     * @return array<int, array<string, mixed>>
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     */
    public function getBreadcrumbsArray(array|ItemInterface|string $menu, array|string|null $subItem = null): array
    {
        return $this->helper->getBreadcrumbsArray($menu, $subItem);
    }

    /**
     * Returns the current item of a menu.
     */
    public function getCurrentItem(ItemInterface|string $menu): ItemInterface
    {
        $rootItem = $this->get($menu);

        $currentItem = $this->helper->getCurrentItem($rootItem);

        if (null === $currentItem) {
            $currentItem = $rootItem;
        }

        return $currentItem;
    }

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     */
    public function pathAsString(ItemInterface $menu, string $separator = ' > '): string
    {
        if (null === $this->menuManipulator) {
            throw new \BadMethodCallException('The menu manipulator must be set to get the breadcrumbs array');
        }

        return $this->menuManipulator->getPathAsString($menu, $separator);
    }

    /**
     * Checks whether an item is current.
     */
    public function isCurrent(ItemInterface $item): bool
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isCurrent($item);
    }

    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param int|null $depth The max depth to look for the item
     */
    public function isAncestor(ItemInterface $item, ?int $depth = null): bool
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isAncestor($item, $depth);
    }
}
